<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\Repository\SmsTemplateRepository;

class SmsTemplateRepositoryTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsTemplateRepository($registry);
        $this->assertInstanceOf(SmsTemplateRepository::class, $repository);
    }

    public function testExtendsServiceEntityRepository(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsTemplateRepository($registry);
        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $repository);
    }

    public function testRepositoryConfiguration(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repository = new SmsTemplateRepository($registry);
        // Test basic repository functionality without requiring database connection
        $this->assertInstanceOf(SmsTemplateRepository::class, $repository);
    }
}
