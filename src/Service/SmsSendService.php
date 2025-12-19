<?php

namespace TencentCloudSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Sms\V20210111\Models\SendStatus;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\MessageStatus;
use TencentCloudSmsBundle\Enum\SendStatus as SmsSendStatus;
use TencentCloudSmsBundle\Exception\JsonEncodingException;
use TencentCloudSmsBundle\Exception\SmsException;

#[WithMonologChannel(channel: 'tencent_cloud_sms')]
final class SmsSendService
{
    public function __construct(
        private readonly SmsClient $smsClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 发送短信给指定接收人
     */
    public function send(SmsRecipient $recipient): void
    {
        $message = $recipient->getMessage();
        if (null === $message) {
            throw new SmsException('短信接收者未关联消息');
        }

        $account = $message->getAccount();
        if (null === $account) {
            throw new SmsException('短信消息未关联账号');
        }

        $client = $this->smsClient->create($account);

        try {
            // 设置发送时间
            $recipient->setSendTime(new \DateTimeImmutable());

            // 构建请求
            $req = new SendSmsRequest();
            $phoneNumber = $recipient->getPhoneNumber();
            $account = $message->getAccount();
            $signature = $message->getSignature();
            $template = $message->getTemplate();
            $templateParams = $message->getTemplateParams();
            $batchId = $message->getBatchId();

            if (null === $phoneNumber || null === $account) {
                throw new SmsException('消息数据不完整，无法发送短信');
            }

            $params = [
                'PhoneNumberSet' => ['+' . $phoneNumber->getPhoneNumber()],
                'SmsSdkAppId' => $account->getSecretId(),
                'SignName' => $signature,
                'TemplateId' => $template,
                'TemplateParamSet' => array_values($templateParams),
                'SessionContext' => $batchId, // 使用批次号作为会话上下文
            ];
            $jsonString = json_encode($params);
            if (false === $jsonString) {
                throw new JsonEncodingException('参数JSON编码失败');
            }
            $req->fromJsonString($jsonString);

            // 发送请求
            $resp = $client->SendSms($req);
            /** @var SendStatus[] $sendStatusSet */
            $sendStatusSet = $resp->getSendStatusSet();
            $sendStatus = $sendStatusSet[0];

            // 更新状态
            $recipient->setSerialNo($sendStatus->getSerialNo());
            $recipient->setCode($sendStatus->getCode());
            $recipient->setStatusMessage($sendStatus->getMessage());

            $serialized = $resp->serialize();
            $decoded = is_string($serialized) ? json_decode($serialized, true) : null;
            /** @var array<string, mixed>|null $typedDecoded */
            $typedDecoded = is_array($decoded) ? $decoded : null;
            $recipient->setRawResponse($typedDecoded);

            $recipient->setStatusTime(new \DateTimeImmutable());

            // 根据返回码设置状态
            $status = match ($sendStatus->getCode()) {
                'Ok' => SmsSendStatus::SUCCESS,
                'LimitExceeded.PhoneNumberDailyLimit' => SmsSendStatus::RATE_LIMIT_EXCEED,
                'FailedOperation.ContainSensitiveWord' => SmsSendStatus::PHONE_NUMBER_LIMIT,
                'FailedOperation.InsufficientBalanceInSmsPackage' => SmsSendStatus::INSUFFICIENT_PACKAGE,
                default => SmsSendStatus::FAIL,
            };
            $recipient->setStatus($status);

            // 更新消息状态
            if (SmsSendStatus::SUCCESS === $status) {
                $message->setStatus(MessageStatus::SUCCESS);
            } else {
                $message->setStatus(MessageStatus::FAILED);
            }

            $this->entityManager->persist($recipient);
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->logger->info('短信发送成功', [
                'messageId' => $message->getId(),
                'phoneNumber' => $phoneNumber->getPhoneNumber(),
                'status' => $status->value,
            ]);
        } catch (TencentCloudSDKException $e) {
            $recipient->setStatus(SmsSendStatus::FAIL);
            $recipient->setCode('Error');
            $recipient->setStatusMessage($e->getMessage());
            $recipient->setStatusTime(new \DateTimeImmutable());

            $message->setStatus(MessageStatus::FAILED);

            $this->entityManager->persist($recipient);
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $phoneNumberForLog = $recipient->getPhoneNumber();
            $this->logger->error('短信发送失败', [
                'messageId' => $message->getId(),
                'phoneNumber' => $phoneNumberForLog?->getPhoneNumber() ?? 'unknown',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
