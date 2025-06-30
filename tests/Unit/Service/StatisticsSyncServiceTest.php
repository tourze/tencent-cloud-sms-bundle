<?php

namespace TencentCloudSmsBundle\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Service\StatisticsSyncService;

class StatisticsSyncServiceTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $entityManager = $this->createMock('Doctrine\ORM\EntityManagerInterface');
        $repository = $this->createMock('TencentCloudSmsBundle\Repository\SmsStatisticsRepository');
        $logger = $this->createMock('Psr\Log\LoggerInterface');
        $smsClient = $this->createMock('TencentCloudSmsBundle\Service\SmsClient');

        $service = new StatisticsSyncService($entityManager, $repository, $logger, $smsClient);
        $this->assertInstanceOf(StatisticsSyncService::class, $service);
    }
}
