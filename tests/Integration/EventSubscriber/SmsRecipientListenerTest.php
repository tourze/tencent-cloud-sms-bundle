<?php

namespace TencentCloudSmsBundle\Tests\Integration\EventSubscriber;

use Doctrine\ORM\Event\PostPersistEventArgs;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\EventSubscriber\SmsRecipientListener;
use TencentCloudSmsBundle\Service\SmsSendService;

class SmsRecipientListenerTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $smsSendService = $this->createMock(SmsSendService::class);
        $listener = new SmsRecipientListener($smsSendService);
        
        $this->assertInstanceOf(SmsRecipientListener::class, $listener);
    }

    public function testPostPersistCallsSmsSendService(): void
    {
        $smsRecipient = $this->createMock(SmsRecipient::class);
        
        $smsSendService = $this->createMock(SmsSendService::class);
        $smsSendService->expects($this->once())
            ->method('send')
            ->with($smsRecipient);
        
        $listener = new SmsRecipientListener($smsSendService);
        
        // 创建一个真实的 PostPersistEventArgs 对象
        $entityManager = $this->createMock('Doctrine\\ORM\\EntityManagerInterface');
        $eventArgs = new PostPersistEventArgs($smsRecipient, $entityManager);
        
        $listener->postPersist($smsRecipient, $eventArgs);
    }
}
