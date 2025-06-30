<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Repository\SmsRecipientRepository;

class SmsRecipientRepositoryTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsRecipientRepository($registry);
        $this->assertInstanceOf(SmsRecipientRepository::class, $repository);
    }

    public function testExtendsServiceEntityRepository(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsRecipientRepository($registry);
        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $repository);
    }

    public function testRepositoryConfiguration(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsRecipientRepository($registry);
        // Test basic repository functionality without requiring database connection
        $this->assertInstanceOf(SmsRecipientRepository::class, $repository);
    }
}
