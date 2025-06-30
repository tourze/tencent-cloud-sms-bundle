<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsStatistics;
use TencentCloudSmsBundle\Repository\SmsStatisticsRepository;

class SmsStatisticsRepositoryTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsStatisticsRepository($registry);
        $this->assertInstanceOf(SmsStatisticsRepository::class, $repository);
    }

    public function testExtendsServiceEntityRepository(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsStatisticsRepository($registry);
        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $repository);
    }

    public function testRepositoryConfiguration(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsStatisticsRepository($registry);
        // Test basic repository functionality without requiring database connection
        $this->assertInstanceOf(SmsStatisticsRepository::class, $repository);
    }
}
