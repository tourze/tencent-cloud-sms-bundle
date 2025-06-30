<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Service\SmsSendService;

class SmsSendServiceTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $smsClient = $this->createMock('TencentCloudSmsBundle\Service\SmsClient');
        $entityManager = $this->createMock('Doctrine\ORM\EntityManagerInterface');
        $logger = $this->createMock('Psr\Log\LoggerInterface');

        $service = new SmsSendService($smsClient, $entityManager, $logger);
        $this->assertInstanceOf(SmsSendService::class, $service);
    }
}
