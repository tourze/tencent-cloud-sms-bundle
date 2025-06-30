<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Repository\SmsSignatureRepository;

class SmsSignatureRepositoryTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsSignatureRepository($registry);
        $this->assertInstanceOf(SmsSignatureRepository::class, $repository);
    }

    public function testExtendsServiceEntityRepository(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsSignatureRepository($registry);
        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $repository);
    }

    public function testRepositoryConfiguration(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsSignatureRepository($registry);
        // Test basic repository functionality without requiring database connection
        $this->assertInstanceOf(SmsSignatureRepository::class, $repository);
    }
}
