<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Service\SmsStatusService;

class SmsStatusServiceTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $smsClient = $this->createMock('TencentCloudSmsBundle\Service\SmsClient');
        $entityManager = $this->createMock('Doctrine\ORM\EntityManagerInterface');
        $smsRecipientRepository = $this->createMock('TencentCloudSmsBundle\Repository\SmsRecipientRepository');
        $logger = $this->createMock('Psr\Log\LoggerInterface');

        $service = new SmsStatusService(
            $smsClient,
            $entityManager,
            $smsRecipientRepository,
            $logger
        );

        $this->assertInstanceOf(SmsStatusService::class, $service);
    }
}
