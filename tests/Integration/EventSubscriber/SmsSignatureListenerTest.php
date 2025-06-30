<?php

namespace TencentCloudSmsBundle\Tests\Integration\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\EventSubscriber\SmsSignatureListener;
use TencentCloudSmsBundle\Service\ImageService;
use TencentCloudSmsBundle\Service\SmsClient;

class SmsSignatureListenerTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $smsClient = $this->createMock(SmsClient::class);
        $imageService = $this->createMock(ImageService::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new SmsSignatureListener($smsClient, $imageService, $logger);
        
        $this->assertInstanceOf(SmsSignatureListener::class, $listener);
    }

    public function testCreateRemoteSignatureSkipsWhenSyncing(): void
    {
        $signature = $this->createMock(SmsSignature::class);
        $signature->expects($this->once())
            ->method('isSyncing')
            ->willReturn(true);
        
        $smsClient = $this->createMock(SmsClient::class);
        $smsClient->expects($this->never())->method('create');
        
        $imageService = $this->createMock(ImageService::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new SmsSignatureListener($smsClient, $imageService, $logger);
        $listener->createRemoteSignature($signature);
    }

    public function testUpdateRemoteSignatureSkipsWhenSyncing(): void
    {
        $signature = $this->createMock(SmsSignature::class);
        $signature->expects($this->once())
            ->method('isSyncing')
            ->willReturn(true);
        
        $smsClient = $this->createMock(SmsClient::class);
        $smsClient->expects($this->never())->method('create');
        
        $imageService = $this->createMock(ImageService::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new SmsSignatureListener($smsClient, $imageService, $logger);
        $listener->updateRemoteSignature($signature);
    }

    public function testDeleteRemoteSignatureSkipsWhenSyncing(): void
    {
        $signature = $this->createMock(SmsSignature::class);
        $signature->expects($this->once())
            ->method('isSyncing')
            ->willReturn(true);
        
        $smsClient = $this->createMock(SmsClient::class);
        $smsClient->expects($this->never())->method('create');
        
        $imageService = $this->createMock(ImageService::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $listener = new SmsSignatureListener($smsClient, $imageService, $logger);
        $listener->deleteRemoteSignature($signature);
    }
}
