<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Service\StatusSyncService;

class StatusSyncServiceTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $smsClient = $this->createMock('TencentCloudSmsBundle\Service\SmsClient');
        $entityManager = $this->createMock('Doctrine\ORM\EntityManagerInterface');
        $smsSignatureRepository = $this->createMock('TencentCloudSmsBundle\Repository\SmsSignatureRepository');
        $smsTemplateRepository = $this->createMock('TencentCloudSmsBundle\Repository\SmsTemplateRepository');
        $logger = $this->createMock('Psr\Log\LoggerInterface');

        $service = new StatusSyncService(
            $smsClient,
            $entityManager,
            $smsSignatureRepository,
            $smsTemplateRepository,
            $logger
        );

        $this->assertInstanceOf(StatusSyncService::class, $service);
    }
}
