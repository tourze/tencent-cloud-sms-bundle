<?php

namespace TencentCloudSmsBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Service\SmsSendService;

#[AsEntityListener(event: Events::postPersist, entity: SmsRecipient::class)]
final class SmsRecipientListener
{
    public function __construct(
        private readonly SmsSendService $smsSendService,
    ) {
    }

    public function postPersist(SmsRecipient $recipient, PostPersistEventArgs $event): void
    {
        // 新建接收人后自动发送短信
        $this->smsSendService->send($recipient);
    }
}
