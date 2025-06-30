<?php

namespace TencentCloudSmsBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\EventSubscriber\SmsTemplateListener;
use TencentCloudSmsBundle\Service\SmsClient;

class SmsTemplateListenerTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $smsClient = $this->createMock(SmsClient::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new SmsTemplateListener($smsClient, $logger);
        
        $this->assertInstanceOf(SmsTemplateListener::class, $listener);
    }

    public function testCreateRemoteTemplateSkipsWhenSyncing(): void
    {
        $template = $this->createMock(SmsTemplate::class);
        $template->expects($this->once())
            ->method('isSyncing')
            ->willReturn(true);
        
        $smsClient = $this->createMock(SmsClient::class);
        $smsClient->expects($this->never())->method('create');
        
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new SmsTemplateListener($smsClient, $logger);
        $listener->createRemoteTemplate($template);
    }

    public function testUpdateRemoteTemplateSkipsWhenSyncing(): void
    {
        $template = $this->createMock(SmsTemplate::class);
        $template->expects($this->once())
            ->method('isSyncing')
            ->willReturn(true);
        
        $smsClient = $this->createMock(SmsClient::class);
        $smsClient->expects($this->never())->method('create');
        
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new SmsTemplateListener($smsClient, $logger);
        $listener->updateRemoteTemplate($template);
    }

    public function testDeleteRemoteTemplateSkipsWhenSyncing(): void
    {
        $template = $this->createMock(SmsTemplate::class);
        $template->expects($this->once())
            ->method('isSyncing')
            ->willReturn(true);
        
        $smsClient = $this->createMock(SmsClient::class);
        $smsClient->expects($this->never())->method('create');
        
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new SmsTemplateListener($smsClient, $logger);
        $listener->deleteRemoteTemplate($template);
    }
}
