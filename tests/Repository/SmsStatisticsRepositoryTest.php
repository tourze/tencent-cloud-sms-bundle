<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsStatistics;
use TencentCloudSmsBundle\Repository\SmsStatisticsRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SmsStatisticsRepository::class)]
#[RunTestsInSeparateProcesses]
final class SmsStatisticsRepositoryTest extends AbstractRepositoryTestCase
{
    private SmsStatisticsRepository $repository;

    public function testCount(): void
    {
        $count = $this->repository->count([]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithCriteria(): void
    {
        $hour = new \DateTimeImmutable('2023-01-01 12:00:00');
        $count = $this->repository->count(['hour' => $hour]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindBy(): void
    {
        $results = $this->repository->findBy([]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsStatistics::class, $result);
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
        $this->assertTrue(null === $result || $result instanceof SmsStatistics);
    }

    public function testFindOneByWithCriteria(): void
    {
        $account = new Account();
        $account->setName('FindOneBy Criteria Test Account');
        $account->setSecretId('findoneby-criteria-id');
        $account->setSecretKey('findoneby-criteria-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $hour = new \DateTimeImmutable('2023-01-01 12:00:00');
        $statistics = new SmsStatistics();
        $statistics->setHour($hour);
        $statistics->setAccount($account);

        $this->repository->save($statistics, true);

        $result = $this->repository->findOneBy(['hour' => $hour]);
        $this->assertNotNull($result);
        $this->assertInstanceOf(SmsStatistics::class, $result);
        $this->assertEquals($hour, $result->getHour());
    }

    public function testFindByHourAndAccount(): void
    {
        $account = new Account();
        $account->setName('Test Statistics Account');
        $account->setSecretId('test-stats-id');
        $account->setSecretKey('test-stats-key');

        $hour = new \DateTimeImmutable('2023-01-01 15:00:00');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $statistics = new SmsStatistics();
        $statistics->setHour($hour);
        $statistics->setAccount($account);

        $this->repository->save($statistics, true);

        $result = $this->repository->findByHourAndAccount($hour, $account);
        if (null !== $result) {
            $this->assertInstanceOf(SmsStatistics::class, $result);
            $this->assertEquals($hour, $result->getHour());
            $this->assertEquals($account, $result->getAccount());
        }
    }

    public function testFindByDateRange(): void
    {
        $account = new Account();
        $account->setName('Test Range Account');
        $account->setSecretId('test-range-id');
        $account->setSecretKey('test-range-key');

        $start = new \DateTimeImmutable('2023-01-01 10:00:00');
        $end = new \DateTimeImmutable('2023-01-01 20:00:00');

        $results = $this->repository->findByDateRange($start, $end, $account);
        $this->assertIsArray($results);

        foreach ($results as $result) {
            $this->assertInstanceOf(SmsStatistics::class, $result);
            $this->assertGreaterThanOrEqual($start, $result->getHour());
            $this->assertLessThanOrEqual($end, $result->getHour());
            $this->assertEquals($account, $result->getAccount());
        }
    }

    public function testSave(): void
    {
        $account = new Account();
        $account->setName('Test Save Statistics Account');
        $account->setSecretId('test-stats-save-id');
        $account->setSecretKey('test-stats-save-key');

        $hour = new \DateTimeImmutable('2023-01-01 16:00:00');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $statistics = new SmsStatistics();
        $statistics->setHour($hour);
        $statistics->setAccount($account);

        $this->repository->save($statistics, false);
        $this->assertNotNull($statistics->getId());
    }

    public function testRemove(): void
    {
        $account = new Account();
        $account->setName('Test Remove Statistics Account');
        $account->setSecretId('test-stats-remove-id');
        $account->setSecretKey('test-stats-remove-key');

        $hour = new \DateTimeImmutable('2023-01-01 17:00:00');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $statistics = new SmsStatistics();
        $statistics->setHour($hour);
        $statistics->setAccount($account);

        $this->repository->save($statistics, true);
        $statisticsId = $statistics->getId();

        $this->repository->remove($statistics, true);

        $removedStatistics = $this->repository->find($statisticsId);
        $this->assertNull($removedStatistics);
    }

    public function testFindAllShouldReturnArrayOfEntities(): void
    {
        $account = new Account();
        $account->setName('Test Account FindAll');
        $account->setSecretId('test-findall-id');
        $account->setSecretKey('test-findall-key');

        $hour1 = new \DateTimeImmutable('2023-01-01 19:00:00');
        $hour2 = new \DateTimeImmutable('2023-01-01 20:00:00');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $statistics1 = new SmsStatistics();
        $statistics1->setHour($hour1);
        $statistics1->setAccount($account);

        $statistics2 = new SmsStatistics();
        $statistics2->setHour($hour2);
        $statistics2->setAccount($account);

        $this->repository->save($statistics1, false);
        $this->repository->save($statistics2, true);

        $result = $this->repository->findAll();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        $foundStatistics = [];
        foreach ($result as $item) {
            $this->assertInstanceOf(SmsStatistics::class, $item);
            if ($hour1 === $item->getHour() || $hour2 === $item->getHour()) {
                $foundStatistics[] = $item;
            }
        }
        $this->assertCount(2, $foundStatistics);
    }

    public function testFindOneByHourShouldReturnMatchingEntity(): void
    {
        $account = new Account();
        $account->setName('Test Account FindOneBy');
        $account->setSecretId('test-findoneby-id');
        $account->setSecretKey('test-findoneby-key');

        $hour = new \DateTimeImmutable('2023-01-01 21:00:00');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $statistics = new SmsStatistics();
        $statistics->setHour($hour);
        $statistics->setAccount($account);

        $this->repository->save($statistics, true);

        $result = $this->repository->findOneBy(['hour' => $hour]);
        $this->assertNotNull($result);
        $this->assertInstanceOf(SmsStatistics::class, $result);
        $this->assertEquals($hour, $result->getHour());
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $account = new Account();
        $account->setName('Association Test Account');
        $account->setSecretId('test-association-id');
        $account->setSecretKey('test-association-key');

        $hour = new \DateTimeImmutable('2023-01-01 22:00:00');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $statistics = new SmsStatistics();
        $statistics->setHour($hour);
        $statistics->setAccount($account);

        $this->repository->save($statistics, true);

        $result = $this->repository->findOneBy(['account' => $account]);
        if (null !== $result) {
            $this->assertInstanceOf(SmsStatistics::class, $result);
            $this->assertEquals($account, $result->getAccount());
        }
    }

    public function testFindOneByWithOrderByShouldReturnCorrectEntity(): void
    {
        $account = new Account();
        $account->setName('FindOneBy Order Test Account');
        $account->setSecretId('findoneby-order-id');
        $account->setSecretKey('findoneby-order-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $hour1 = new \DateTimeImmutable('2023-01-08 10:00:00');
        $hour2 = new \DateTimeImmutable('2023-01-08 11:00:00');
        $hour3 = new \DateTimeImmutable('2023-01-08 12:00:00');

        $statistics1 = new SmsStatistics();
        $statistics1->setHour($hour2); // 中间时间
        $statistics1->setAccount($account);

        $statistics2 = new SmsStatistics();
        $statistics2->setHour($hour3); // 最晚时间
        $statistics2->setAccount($account);

        $statistics3 = new SmsStatistics();
        $statistics3->setHour($hour1); // 最早时间
        $statistics3->setAccount($account);

        $this->repository->save($statistics1, false);
        $this->repository->save($statistics2, false);
        $this->repository->save($statistics3, true);

        // 按升序查找，应该返回最早的记录
        $earliestResult = $this->repository->findOneBy(['account' => $account], ['hour' => 'ASC']);
        $this->assertNotNull($earliestResult);
        $this->assertInstanceOf(SmsStatistics::class, $earliestResult);
        $this->assertEquals($hour1, $earliestResult->getHour());

        // 按降序查找，应该返回最晚的记录
        $latestResult = $this->repository->findOneBy(['account' => $account], ['hour' => 'DESC']);
        $this->assertNotNull($latestResult);
        $this->assertInstanceOf(SmsStatistics::class, $latestResult);
        $this->assertEquals($hour3, $latestResult->getHour());
    }

    public function testFindByWithAssociationFieldShouldReturnMatchingEntities(): void
    {
        $account1 = new Account();
        $account1->setName('Association Test Account 1');
        $account1->setSecretId('association-test-id-1');
        $account1->setSecretKey('association-test-key-1');

        $account2 = new Account();
        $account2->setName('Association Test Account 2');
        $account2->setSecretId('association-test-id-2');
        $account2->setSecretKey('association-test-key-2');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account1);
        $entityManager->persist($account2);
        $entityManager->flush();

        $hour = new \DateTimeImmutable('2023-01-09 15:00:00');

        $statistics1 = new SmsStatistics();
        $statistics1->setHour($hour);
        $statistics1->setAccount($account1);

        $statistics2 = new SmsStatistics();
        $statistics2->setHour($hour);
        $statistics2->setAccount($account2);

        $this->repository->save($statistics1, false);
        $this->repository->save($statistics2, true);

        // 查找特定 account 的统计
        $account1Results = $this->repository->findBy(['account' => $account1]);
        $this->assertIsArray($account1Results);
        foreach ($account1Results as $result) {
            $this->assertInstanceOf(SmsStatistics::class, $result);
            $this->assertEquals($account1, $result->getAccount());
        }

        $account2Results = $this->repository->findBy(['account' => $account2]);
        $this->assertIsArray($account2Results);
        foreach ($account2Results as $result) {
            $this->assertInstanceOf(SmsStatistics::class, $result);
            $this->assertEquals($account2, $result->getAccount());
        }

        // 验证结果不同
        $this->assertNotEquals(
            array_map(fn ($s) => $s->getId(), $account1Results),
            array_map(fn ($s) => $s->getId(), $account2Results)
        );
    }

    public function testCountWithAssociationFieldShouldReturnCorrectNumber(): void
    {
        $account = new Account();
        $account->setName('Count Association Test Account');
        $account->setSecretId('count-association-id');
        $account->setSecretKey('count-association-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $hour1 = new \DateTimeImmutable('2023-01-10 10:00:00');
        $hour2 = new \DateTimeImmutable('2023-01-10 11:00:00');

        $statistics1 = new SmsStatistics();
        $statistics1->setHour($hour1);
        $statistics1->setAccount($account);

        $statistics2 = new SmsStatistics();
        $statistics2->setHour($hour2);
        $statistics2->setAccount($account);

        $this->repository->save($statistics1, false);
        $this->repository->save($statistics2, true);

        $accountCount = $this->repository->count(['account' => $account]);
        $this->assertIsInt($accountCount);
        $this->assertGreaterThanOrEqual(2, $accountCount);

        // 验证与不同 account 的计数不同
        $differentAccount = new Account();
        $differentAccount->setName('Different Account');
        $differentAccount->setSecretId('different-id');
        $differentAccount->setSecretKey('different-key');
        $entityManager->persist($differentAccount);
        $entityManager->flush();

        $differentAccountCount = $this->repository->count(['account' => $differentAccount]);
        $this->assertIsInt($differentAccountCount);
        $this->assertSame(0, $differentAccountCount);
    }

    public function testFindByWithNullableFieldShouldHandleNullValues(): void
    {
        // 创建有效和无效的 account
        $validAccount = new Account();
        $validAccount->setName('Valid Account');
        $validAccount->setSecretId('valid-account-id');
        $validAccount->setSecretKey('valid-account-key');
        $validAccount->setValid(true);

        $invalidAccount = new Account();
        $invalidAccount->setName('Invalid Account');
        $invalidAccount->setSecretId('invalid-account-id');
        $invalidAccount->setSecretKey('invalid-account-key');
        $invalidAccount->setValid(false);

        $nullValidAccount = new Account();
        $nullValidAccount->setName('Null Valid Account');
        $nullValidAccount->setSecretId('null-valid-id');
        $nullValidAccount->setSecretKey('null-valid-key');
        $nullValidAccount->setValid(null);

        $entityManager = self::getEntityManager();
        $entityManager->persist($validAccount);
        $entityManager->persist($invalidAccount);
        $entityManager->persist($nullValidAccount);
        $entityManager->flush();

        $hour = new \DateTimeImmutable('2023-01-11 14:00:00');

        $validStatistics = new SmsStatistics();
        $validStatistics->setHour($hour);
        $validStatistics->setAccount($validAccount);

        $invalidStatistics = new SmsStatistics();
        $invalidStatistics->setHour($hour);
        $invalidStatistics->setAccount($invalidAccount);

        $nullStatistics = new SmsStatistics();
        $nullStatistics->setHour($hour);
        $nullStatistics->setAccount($nullValidAccount);

        $this->repository->save($validStatistics, false);
        $this->repository->save($invalidStatistics, false);
        $this->repository->save($nullStatistics, true);

        // 通过关联实体的可空字段查询
        $qb = $this->repository->createQueryBuilder('s');
        $qb->join('s.account', 'a')
            ->where('a.valid IS NULL')
        ;
        $nullResults = $qb->getQuery()->getResult();

        $this->assertIsArray($nullResults);
        $foundNullStatistics = false;
        foreach ($nullResults as $result) {
            $this->assertInstanceOf(SmsStatistics::class, $result);
            if ($result->getId() === $nullStatistics->getId()) {
                $foundNullStatistics = true;
                $account = $result->getAccount();
                $this->assertNotNull($account, 'Account should not be null for this test case');
                $this->assertNull($account->isValid());
            }
        }
        $this->assertTrue($foundNullStatistics);
    }

    public function testCountWithNullableFieldShouldReturnCorrectNumber(): void
    {
        // 创建不同 valid 状态的 account
        $validAccount = new Account();
        $validAccount->setName('Count Valid Account');
        $validAccount->setSecretId('count-valid-id');
        $validAccount->setSecretKey('count-valid-key');
        $validAccount->setValid(true);

        $nullValidAccount = new Account();
        $nullValidAccount->setName('Count Null Valid Account');
        $nullValidAccount->setSecretId('count-null-valid-id');
        $nullValidAccount->setSecretKey('count-null-valid-key');
        $nullValidAccount->setValid(null);

        $entityManager = self::getEntityManager();
        $entityManager->persist($validAccount);
        $entityManager->persist($nullValidAccount);
        $entityManager->flush();

        $hour = new \DateTimeImmutable('2023-01-12 16:00:00');

        $validStatistics = new SmsStatistics();
        $validStatistics->setHour($hour);
        $validStatistics->setAccount($validAccount);

        $nullStatistics = new SmsStatistics();
        $nullStatistics->setHour($hour);
        $nullStatistics->setAccount($nullValidAccount);

        $this->repository->save($validStatistics, false);
        $this->repository->save($nullStatistics, true);

        // 计算关联实体 valid 字段为 null 的记录数
        $qb = $this->repository->createQueryBuilder('s');
        $qb->select('COUNT(s.id)')
            ->join('s.account', 'a')
            ->where('a.valid IS NULL')
        ;
        $nullCount = (int) $qb->getQuery()->getSingleScalarResult();

        $this->assertIsInt($nullCount);
        $this->assertGreaterThanOrEqual(1, $nullCount);

        // 计算关联实体 valid 字段为 true 的记录数
        $qb2 = $this->repository->createQueryBuilder('s');
        $qb2->select('COUNT(s.id)')
            ->join('s.account', 'a')
            ->where('a.valid = :valid')
            ->setParameter('valid', true)
        ;
        $trueCount = (int) $qb2->getQuery()->getSingleScalarResult();

        $this->assertIsInt($trueCount);
        $this->assertGreaterThanOrEqual(1, $trueCount);

        // 验证两个计数结果不同，说明查询条件有效
        $this->assertNotEquals($nullCount, $trueCount);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SmsStatisticsRepository::class);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setSecretId('test-secret-id-' . uniqid());
        $account->setSecretKey('test-secret-key-' . uniqid());

        $entity = new SmsStatistics();
        $entity->setHour(new \DateTimeImmutable('2023-01-01 12:00:00'));
        $entity->setAccount($account);

        return $entity;
    }

    protected function getRepository(): SmsStatisticsRepository
    {
        return $this->repository;
    }
}
