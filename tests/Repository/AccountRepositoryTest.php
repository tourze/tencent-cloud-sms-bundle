<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Repository\AccountRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    private AccountRepository $repository;

    public function testCount(): void
    {
        $count = $this->repository->count([]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithCriteria(): void
    {
        $count = $this->repository->count(['valid' => true]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindBy(): void
    {
        $results = $this->repository->findBy([]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(Account::class, $result);
        }
    }

    public function testFindByWithLimit(): void
    {
        $results = $this->repository->findBy([], null, 5);
        $this->assertIsArray($results);
        $this->assertLessThanOrEqual(5, count($results));
    }

    public function testFindByWithOffset(): void
    {
        $results = $this->repository->findBy([], null, null, 1);
        $this->assertIsArray($results);
    }

    public function testFindOneBy(): void
    {
        $result = $this->repository->findOneBy([]);
        $this->assertTrue(null === $result || $result instanceof Account);
    }

    public function testFindOneByWithCriteria(): void
    {
        $result = $this->repository->findOneBy(['valid' => true]);
        if (null !== $result) {
            $this->assertInstanceOf(Account::class, $result);
            $this->assertTrue($result->isValid());
        }
    }

    public function testSave(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        $this->repository->save($account, false);
        $this->assertNotNull($account->getId());
    }

    public function testRemove(): void
    {
        $account = new Account();
        $account->setName('Test Account for Removal');
        $account->setSecretId('test-secret-id-remove');
        $account->setSecretKey('test-secret-key-remove');
        $account->setValid(false);

        $this->repository->save($account, true);
        $accountId = $account->getId();

        $this->repository->remove($account, true);

        $removedAccount = $this->repository->find($accountId);
        $this->assertNull($removedAccount);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AccountRepository::class);
    }

    protected function createNewEntity(): object
    {
        $entity = new Account();
        $entity->setName('Test Account ' . uniqid());
        $entity->setSecretId('test-secret-id-' . uniqid());
        $entity->setSecretKey('test-secret-key-' . uniqid());
        $entity->setValid(true);

        return $entity;
    }

    protected function getRepository(): AccountRepository
    {
        return $this->repository;
    }
}
