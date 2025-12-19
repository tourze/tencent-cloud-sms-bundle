<?php

namespace TencentCloudSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\PullSmsSendStatusByPhoneNumberRequest;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\SendStatus;
use TencentCloudSmsBundle\Exception\JsonEncodingException;
use TencentCloudSmsBundle\Repository\SmsRecipientRepository;

#[WithMonologChannel(channel: 'tencent_cloud_sms')]
final class SmsStatusService
{
    public function __construct(
        private readonly SmsClient $smsClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly SmsRecipientRepository $recipientRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 同步指定账号下的短信发送状态
     * 只同步 1 小时内发送但没有结果的记录
     */
    public function syncStatus(): void
    {
        // 查找 1 小时内发送但没有接收时间的记录
        $oneHourAgo = new \DateTime('-1 hour');
        $recipients = $this->recipientRepository->findNeedSyncStatus($oneHourAgo);

        // 按账号分组
        /** @var array<int, SmsRecipient[]> $groupedRecipients */
        $groupedRecipients = [];
        foreach ($recipients as $recipient) {
            $message = $recipient->getMessage();
            if (null === $message) {
                continue;
            }
            $account = $message->getAccount();
            if (null === $account) {
                continue;
            }
            $accountId = $account->getId();
            $groupedRecipients[$accountId][] = $recipient;
        }

        // 按账号同步状态
        foreach ($groupedRecipients as $accountRecipients) {
            $this->syncStatusByAccount($accountRecipients);
        }
    }

    /**
     * 同步未知状态的短信记录
     */
    public function syncUnknownStatus(int $limit = 100): void
    {
        // 查找未知状态的记录
        $recipients = $this->recipientRepository->findUnknownStatus($limit);

        // 按账号分组
        /** @var array<int, SmsRecipient[]> $groupedRecipients */
        $groupedRecipients = [];
        foreach ($recipients as $recipient) {
            $message = $recipient->getMessage();
            if (null === $message) {
                continue;
            }
            $account = $message->getAccount();
            if (null === $account) {
                continue;
            }
            $accountId = $account->getId();
            $groupedRecipients[$accountId][] = $recipient;
        }

        // 按账号同步状态
        foreach ($groupedRecipients as $accountRecipients) {
            $this->syncStatusByAccount($accountRecipients);
        }
    }

    /**
     * 同步指定账号下的短信发送状态
     *
     * @param SmsRecipient[] $recipients
     */
    private function syncStatusByAccount(array $recipients): void
    {
        if (0 === count($recipients)) {
            return;
        }

        $message = $recipients[0]->getMessage();
        if (null === $message) {
            return;
        }
        $account = $message->getAccount();
        if (null === $account) {
            return;
        }
        $client = $this->smsClient->create($account);

        try {
            $pullStatusSet = $this->fetchStatusFromTencent($client, $account, $recipients);
            $this->updateRecipientsStatus($recipients, $pullStatusSet);
            $this->entityManager->flush();
        } catch (TencentCloudSDKException $e) {
            $this->handleSyncError($account, $e);
        }
    }

    /**
     * @param \TencentCloud\Sms\V20210111\SmsClient $client
     * @param SmsRecipient[] $recipients
     * @return array<mixed>
     */
    private function fetchStatusFromTencent($client, Account $account, array $recipients): array
    {
        $req = new PullSmsSendStatusByPhoneNumberRequest();
        $params = [
            'SendDateTime' => date('YmdHis', strtotime('-1 hour')),
            'Offset' => 0,
            'Limit' => 100,
            'PhoneNumber' => '+' . ($recipients[0]->getPhoneNumber()?->getPhoneNumber() ?? ''),
            'SmsSdkAppId' => $account->getSecretId(),
        ];
        $jsonString = json_encode($params);
        if (false === $jsonString) {
            throw new JsonEncodingException('JSON编码失败');
        }
        $req->fromJsonString($jsonString);

        $resp = $client->PullSmsSendStatusByPhoneNumber($req);
        $statusSet = $resp->getPullSmsSendStatusSet();

        return is_array($statusSet) ? $statusSet : [];
    }

    /**
     * @param SmsRecipient[] $recipients
     * @param array<mixed> $pullStatusSet
     */
    private function updateRecipientsStatus(array $recipients, array $pullStatusSet): void
    {
        $serialNoMap = $this->buildSerialNoMap($recipients);

        foreach ($pullStatusSet as $pullStatus) {
            if (!is_object($pullStatus) || !method_exists($pullStatus, 'getSerialNo')) {
                continue;
            }

            $serialNo = $pullStatus->getSerialNo();
            if (!is_string($serialNo)) {
                continue;
            }

            $recipient = $serialNoMap[$serialNo] ?? null;
            if (null === $recipient) {
                continue;
            }

            $this->updateRecipientFromPullStatus($recipient, $pullStatus);
        }
    }

    /**
     * @param SmsRecipient[] $recipients
     * @return array<string, SmsRecipient>
     */
    private function buildSerialNoMap(array $recipients): array
    {
        $serialNoMap = [];
        foreach ($recipients as $recipient) {
            if (null !== $recipient->getSerialNo() && '' !== $recipient->getSerialNo()) {
                $serialNoMap[$recipient->getSerialNo()] = $recipient;
            }
        }

        return $serialNoMap;
    }

    /**
     * @param object $pullStatus
     */
    private function updateRecipientFromPullStatus(SmsRecipient $recipient, object $pullStatus): void
    {
        if (
            !method_exists($pullStatus, 'getReportStatus')
            || !method_exists($pullStatus, 'getDescription')
            || !method_exists($pullStatus, 'getUserReceiveTime')
            || !method_exists($pullStatus, 'serialize')
        ) {
            return;
        }

        $reportStatus = $pullStatus->getReportStatus();
        $description = $pullStatus->getDescription();
        $userReceiveTime = $pullStatus->getUserReceiveTime();

        if (!is_string($reportStatus) && null !== $reportStatus) {
            return;
        }
        if (!is_string($description) && null !== $description) {
            return;
        }

        $recipient->setCode($reportStatus);
        $recipient->setStatusMessage($description);

        if (is_string($userReceiveTime) || is_int($userReceiveTime)) {
            $recipient->setReceiveTime(new \DateTimeImmutable((string) $userReceiveTime));
        }

        $recipient->setStatusTime(new \DateTimeImmutable());

        $serialized = $pullStatus->serialize();
        if (is_array($serialized)) {
            /** @var array<string, mixed> $typedSerialized */
            $typedSerialized = $serialized;
            $recipient->setRawResponse($typedSerialized);
        }

        if (is_string($reportStatus)) {
            $status = $this->mapStatusFromCode($reportStatus);
            $recipient->setStatus($status);
            $this->logStatusUpdate($recipient, $status);
        }

        $this->entityManager->persist($recipient);
    }

    private function mapStatusFromCode(string $reportStatus): SendStatus
    {
        return match ($reportStatus) {
            'SUCCESS' => SendStatus::SUCCESS,
            'RATE_LIMIT_EXCEED' => SendStatus::RATE_LIMIT_EXCEED,
            'MOBILE_BLACK' => SendStatus::PHONE_NUMBER_LIMIT,
            'INSUFFICIENT_PACKAGE' => SendStatus::INSUFFICIENT_PACKAGE,
            default => SendStatus::FAIL,
        };
    }

    private function logStatusUpdate(SmsRecipient $recipient, SendStatus $status): void
    {
        $message = $recipient->getMessage();
        $phoneNumber = $recipient->getPhoneNumber();
        $this->logger->info('短信状态同步成功', [
            'messageId' => $message?->getId(),
            'phoneNumber' => $phoneNumber?->getPhoneNumber(),
            'status' => $status->value,
        ]);
    }

    private function handleSyncError(Account $account, TencentCloudSDKException $e): void
    {
        $this->logger->error('短信状态同步失败', [
            'account' => $account->getId(),
            'error' => $e->getMessage(),
        ]);
    }
}
