<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsMessage;
use TencentCloudSmsBundle\Repository\SmsMessageRepository;

class SmsMessageRepositoryTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsMessageRepository($registry);
        $this->assertInstanceOf(SmsMessageRepository::class, $repository);
    }

    public function testExtendsServiceEntityRepository(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsMessageRepository($registry);
        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $repository);
    }

    public function testRepositoryConfiguration(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsMessageRepository($registry);
        // Test basic repository functionality without requiring database connection
        $this->assertInstanceOf(SmsMessageRepository::class, $repository);
    }
}
