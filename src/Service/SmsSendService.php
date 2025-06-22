<?php

namespace TencentCloudSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Sms\V20210111\Models\SendStatus;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\MessageStatus;
use TencentCloudSmsBundle\Enum\SendStatus as SmsSendStatus;

class SmsSendService
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
        $client = $this->smsClient->create($message->getAccount());

        try {
            // 设置发送时间
            $recipient->setSendTime(new \DateTimeImmutable());

            // 构建请求
            $req = new SendSmsRequest();
            $params = [
                'PhoneNumberSet' => ['+' . $recipient->getPhoneNumber()->getPhoneNumber()],
                'SmsSdkAppId' => $message->getAccount()->getSecretId(),
                'SignName' => $message->getSignature(),
                'TemplateId' => $message->getTemplate(),
                'TemplateParamSet' => array_values($message->getTemplateParams()),
                'SessionContext' => $message->getBatchId(), // 使用批次号作为会话上下文
            ];
            $req->fromJsonString(json_encode($params));

            // 发送请求
            $resp = $client->SendSms($req);
            /** @var SendStatus[] $sendStatusSet */
            $sendStatusSet = $resp->getSendStatusSet();
            $sendStatus = $sendStatusSet[0];

            // 更新状态
            $recipient
                ->setSerialNo($sendStatus->getSerialNo())
                ->setCode($sendStatus->getCode())
                ->setStatusMessage($sendStatus->getMessage())
                ->setRawResponse($resp->serialize())
                ->setStatusTime(new \DateTimeImmutable());

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
            if ($status === SmsSendStatus::SUCCESS) {
                $message->setStatus(MessageStatus::SUCCESS);
            } else {
                $message->setStatus(MessageStatus::FAILED);
            }

            $this->entityManager->persist($recipient);
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->logger->info('短信发送成功', [
                'messageId' => $message->getId(),
                'phoneNumber' => $recipient->getPhoneNumber()->getPhoneNumber(),
                'status' => $status->value,
            ]);
        } catch (TencentCloudSDKException $e) {
            $recipient
                ->setStatus(SmsSendStatus::FAIL)
                ->setCode('Error')
                ->setStatusMessage($e->getMessage())
                ->setStatusTime(new \DateTimeImmutable());

            $message->setStatus(MessageStatus::FAILED);

            $this->entityManager->persist($recipient);
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->logger->error('短信发送失败', [
                'messageId' => $message->getId(),
                'phoneNumber' => $recipient->getPhoneNumber()->getPhoneNumber(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
