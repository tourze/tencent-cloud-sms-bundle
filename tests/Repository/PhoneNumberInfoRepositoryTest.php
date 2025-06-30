<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Repository\PhoneNumberInfoRepository;

class PhoneNumberInfoRepositoryTest extends TestCase
{
    private PhoneNumberInfoRepository $repository;
    private ManagerRegistry $registry;

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(PhoneNumberInfoRepository::class, $this->repository);
    }

    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $this->repository);
    }

    public function testRepositoryConfiguration(): void
    {
        // Test basic repository functionality without requiring database connection
        $this->assertInstanceOf(PhoneNumberInfoRepository::class, $this->repository);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new PhoneNumberInfoRepository($this->registry);
    }
}
