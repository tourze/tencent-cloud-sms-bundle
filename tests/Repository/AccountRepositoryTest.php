<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Repository\AccountRepository;

class AccountRepositoryTest extends TestCase
{
    private AccountRepository $repository;
    private ManagerRegistry $registry;

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AccountRepository::class, $this->repository);
    }

    public function testExtendsServiceEntityRepository(): void
    {
        $this->assertInstanceOf('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository', $this->repository);
    }

    public function testRepositoryConfiguration(): void
    {
        // Test basic repository functionality without requiring database connection
        $this->assertInstanceOf(AccountRepository::class, $this->repository);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new AccountRepository($this->registry);
    }
}
