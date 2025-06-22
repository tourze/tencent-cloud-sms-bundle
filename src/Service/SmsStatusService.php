<?php

namespace TencentCloudSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\PullSmsSendStatus;
use TencentCloud\Sms\V20210111\Models\PullSmsSendStatusByPhoneNumberRequest;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\SendStatus;
use TencentCloudSmsBundle\Repository\SmsRecipientRepository;

class SmsStatusService
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
        $groupedRecipients = [];
        foreach ($recipients as $recipient) {
            $accountId = $recipient->getMessage()->getAccount()->getId();
            $groupedRecipients[$accountId][] = $recipient;
        }

        // 按账号同步状态
        foreach ($groupedRecipients as $accountId => $accountRecipients) {
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
        $groupedRecipients = [];
        foreach ($recipients as $recipient) {
            $accountId = $recipient->getMessage()->getAccount()->getId();
            $groupedRecipients[$accountId][] = $recipient;
        }

        // 按账号同步状态
        foreach ($groupedRecipients as $accountId => $accountRecipients) {
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
        if (empty($recipients)) {
            return;
        }

        $account = $recipients[0]->getMessage()->getAccount();
        $client = $this->smsClient->create($account);

        try {
            // 构建请求
            $req = new PullSmsSendStatusByPhoneNumberRequest();
            $params = [
                'SendDateTime' => date('YmdHis', strtotime('-1 hour')), // 拉取最近 1 小时的数据
                'Offset' => 0,
                'Limit' => 100,
                'PhoneNumber' => '+' . $recipients[0]->getPhoneNumber()->getPhoneNumber(),
                'SmsSdkAppId' => $account->getSecretId(),
            ];
            $req->fromJsonString(json_encode($params));

            // 发送请求
            $resp = $client->PullSmsSendStatusByPhoneNumber($req);
            /** @var PullSmsSendStatus[] $pullStatusSet */
            $pullStatusSet = $resp->getPullSmsSendStatusSet();

            // 建立序列号映射，方便更新
            $serialNoMap = [];
            foreach ($recipients as $recipient) {
                if ($recipient->getSerialNo() !== null && $recipient->getSerialNo() !== '') {
                    $serialNoMap[$recipient->getSerialNo()] = $recipient;
                }
            }

            foreach ($pullStatusSet as $pullStatus) {
                $recipient = $serialNoMap[$pullStatus->getSerialNo()] ?? null;
                if ($recipient === null) {
                    continue;
                }

                // 更新状态
                $recipient
                    ->setCode($pullStatus->getReportStatus())
                    ->setStatusMessage($pullStatus->getDescription())
                    ->setReceiveTime(new \DateTimeImmutable((string) $pullStatus->getUserReceiveTime()))
                    ->setStatusTime(new \DateTimeImmutable())
                    ->setRawResponse($pullStatus->serialize());

                // 根据返回码设置状态
                $status = match ($pullStatus->getReportStatus()) {
                    'SUCCESS' => SendStatus::SUCCESS,
                    'RATE_LIMIT_EXCEED' => SendStatus::RATE_LIMIT_EXCEED,
                    'MOBILE_BLACK' => SendStatus::PHONE_NUMBER_LIMIT,
                    'INSUFFICIENT_PACKAGE' => SendStatus::INSUFFICIENT_PACKAGE,
                    default => SendStatus::FAIL,
                };
                $recipient->setStatus($status);

                $this->entityManager->persist($recipient);

                $this->logger->info('短信状态同步成功', [
                    'messageId' => $recipient->getMessage()->getId(),
                    'phoneNumber' => $recipient->getPhoneNumber()->getPhoneNumber(),
                    'status' => $status->value,
                ]);
            }

            $this->entityManager->flush();
        } catch (TencentCloudSDKException $e) {
            $this->logger->error('短信状态同步失败', [
                'account' => $account->getId(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
