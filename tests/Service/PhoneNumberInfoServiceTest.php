<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Service\PhoneNumberInfoService;

class PhoneNumberInfoServiceTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $smsClient = $this->createMock('TencentCloudSmsBundle\Service\SmsClient');
        $entityManager = $this->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this->createMock('TencentCloudSmsBundle\Repository\PhoneNumberInfoRepository');
        $logger = $this->createMock('Psr\Log\LoggerInterface');

        $service = new PhoneNumberInfoService($smsClient, $entityManager, $repository, $logger);
        $this->assertInstanceOf(PhoneNumberInfoService::class, $service);
    }
} 