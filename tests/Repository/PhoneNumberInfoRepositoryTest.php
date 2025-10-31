<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Repository\PhoneNumberInfoRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(PhoneNumberInfoRepository::class)]
#[RunTestsInSeparateProcesses]
final class PhoneNumberInfoRepositoryTest extends AbstractRepositoryTestCase
{
    private PhoneNumberInfoRepository $repository;

    public function testCount(): void
    {
        $count = $this->repository->count([]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithCriteria(): void
    {
        $count = $this->repository->count(['nationCode' => '+86']);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindBy(): void
    {
        $results = $this->repository->findBy([]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(PhoneNumberInfo::class, $result);
        }
    }

    public function testFindByWithLimit(): void
    {
        $results = $this->repository->findBy([], null, 3);
        $this->assertIsArray($results);
        $this->assertLessThanOrEqual(3, count($results));
    }

    public function testFindByWithOffset(): void
    {
        $results = $this->repository->findBy([], null, null, 1);
        $this->assertIsArray($results);
    }

    public function testFindOneBy(): void
    {
        $result = $this->repository->findOneBy([]);
        $this->assertTrue(null === $result || $result instanceof PhoneNumberInfo);
    }

    public function testFindOneByWithCriteria(): void
    {
        $result = $this->repository->findOneBy(['nationCode' => '+86']);
        $this->assertTrue(null === $result || $result instanceof PhoneNumberInfo);
        if (null !== $result) {
            $this->assertEquals('+86', $result->getNationCode());
        }
    }

    public function testFindByPhoneNumber(): void
    {
        $phoneNumber = $this->generateUniquePhoneNumber();
        $phoneInfo = new PhoneNumberInfo();
        $phoneInfo->setPhoneNumber($phoneNumber);
        $phoneInfo->setNationCode('+86');
        $this->repository->save($phoneInfo, true);

        $result = $this->repository->findByPhoneNumber($phoneNumber);
        $this->assertInstanceOf(PhoneNumberInfo::class, $result);
        $this->assertEquals($phoneNumber, $result->getPhoneNumber());
    }

    public function testFindByPhoneNumberWithNationCode(): void
    {
        $phoneNumber = $this->generateUniquePhoneNumber('139');
        $phoneInfo = new PhoneNumberInfo();
        $phoneInfo->setPhoneNumber($phoneNumber);
        $phoneInfo->setNationCode('+86');
        $this->repository->save($phoneInfo, true);

        $result = $this->repository->findByPhoneNumber($phoneNumber, '+86');
        $this->assertInstanceOf(PhoneNumberInfo::class, $result);
        $this->assertEquals($phoneNumber, $result->getPhoneNumber());
        $this->assertEquals('+86', $result->getNationCode());
    }

    public function testSave(): void
    {
        $phoneInfo = new PhoneNumberInfo();
        $phoneInfo->setPhoneNumber($this->generateUniquePhoneNumber('137'));
        $phoneInfo->setNationCode('+86');
        $phoneInfo->setIsoCode('CN');

        $this->repository->save($phoneInfo, false);
        $this->assertNotNull($phoneInfo->getId());
    }

    public function testRemove(): void
    {
        $phoneInfo = new PhoneNumberInfo();
        $phoneInfo->setPhoneNumber($this->generateUniquePhoneNumber('136'));
        $phoneInfo->setNationCode('+86');

        $this->repository->save($phoneInfo, true);
        $phoneInfoId = $phoneInfo->getId();

        $this->repository->remove($phoneInfo, true);

        $removedPhoneInfo = $this->repository->find($phoneInfoId);
        $this->assertNull($removedPhoneInfo);
    }

    public function testFindOneByWithOrderByClauseShouldReturnFirstMatchingEntity(): void
    {
        // 先清理相关数据
        $existingRecords = $this->repository->findBy(['nationCode' => '+33']);
        foreach ($existingRecords as $record) {
            $this->repository->remove($record, false);
        }
        self::getEntityManager()->flush();

        // 创建两个电话号码，确保它们有明确的顺序
        $phoneNumber1 = $this->generateUniquePhoneNumber('012');
        $phoneNumber2 = $this->generateUniquePhoneNumber('012');

        // 确保两个号码的顺序是确定的
        if ($phoneNumber1 > $phoneNumber2) {
            [$phoneNumber1, $phoneNumber2] = [$phoneNumber2, $phoneNumber1];
        }

        $phoneInfo1 = new PhoneNumberInfo();
        $phoneInfo1->setPhoneNumber($phoneNumber1);
        $phoneInfo1->setNationCode('+33');
        $phoneInfo1->setIsoCode('FR');
        $this->repository->save($phoneInfo1, false);

        $phoneInfo2 = new PhoneNumberInfo();
        $phoneInfo2->setPhoneNumber($phoneNumber2);
        $phoneInfo2->setNationCode('+33');
        $phoneInfo2->setIsoCode('FR');
        $this->repository->save($phoneInfo2, true);

        // 测试按phoneNumber升序返回第一个（应该是较小的号码）
        $result = $this->repository->findOneBy(['nationCode' => '+33'], ['phoneNumber' => 'ASC']);
        $this->assertNotNull($result);
        $this->assertInstanceOf(PhoneNumberInfo::class, $result);
        $this->assertEquals($phoneNumber1, $result->getPhoneNumber());

        // 测试按phoneNumber降序返回第一个（应该是较大的号码）
        $result = $this->repository->findOneBy(['nationCode' => '+33'], ['phoneNumber' => 'DESC']);
        $this->assertNotNull($result);
        $this->assertInstanceOf(PhoneNumberInfo::class, $result);
        $this->assertEquals($phoneNumber2, $result->getPhoneNumber());
    }

    public function testFindByWithNullFieldQueryShouldReturnMatchingEntities(): void
    {
        // 创建一个 isoCode 为 null 的记录
        $testPhoneNumber = $this->generateUniquePhoneNumber('135');
        $phoneInfo1 = new PhoneNumberInfo();
        $phoneInfo1->setPhoneNumber($testPhoneNumber);
        $phoneInfo1->setNationCode('+86');
        $phoneInfo1->setIsoCode(null);
        $this->repository->save($phoneInfo1, false);

        // 创建一个 isoCode 不为 null 的记录
        $phoneInfo2 = new PhoneNumberInfo();
        $phoneInfo2->setPhoneNumber($this->generateUniquePhoneNumber('135'));
        $phoneInfo2->setNationCode('+86');
        $phoneInfo2->setIsoCode('CN');
        $this->repository->save($phoneInfo2, true);

        // 查询 isoCode 为 null 的记录
        $nullResults = $this->repository->findBy(['isoCode' => null]);
        $this->assertIsArray($nullResults);

        $foundTestRecord = false;
        foreach ($nullResults as $result) {
            $this->assertInstanceOf(PhoneNumberInfo::class, $result);
            $this->assertNull($result->getIsoCode());
            if ($testPhoneNumber === $result->getPhoneNumber()) {
                $foundTestRecord = true;
            }
        }
        $this->assertTrue($foundTestRecord, '应该找到测试创建的 isoCode 为 null 的记录');
    }

    public function testCountWithNullFieldQueryShouldReturnCorrectNumber(): void
    {
        // 先获取当前 isoName 为 null 的记录数量
        $initialNullCount = $this->repository->count(['isoName' => null]);

        // 创建 isoName 为 null 的记录
        $phoneInfo1 = new PhoneNumberInfo();
        $phoneInfo1->setPhoneNumber($this->generateUniquePhoneNumber('135'));
        $phoneInfo1->setNationCode('+86');
        $phoneInfo1->setIsoCode('CN');
        $phoneInfo1->setIsoName(null);
        $this->repository->save($phoneInfo1, false);

        // 创建 isoName 不为 null 的记录
        $phoneInfo2 = new PhoneNumberInfo();
        $phoneInfo2->setPhoneNumber($this->generateUniquePhoneNumber('135'));
        $phoneInfo2->setNationCode('+86');
        $phoneInfo2->setIsoCode('CN');
        $phoneInfo2->setIsoName('中国');
        $this->repository->save($phoneInfo2, true);

        $finalNullCount = $this->repository->count(['isoName' => null]);
        $this->assertIsInt($finalNullCount);
        $this->assertEquals($initialNullCount + 1, $finalNullCount);

        // 验证非null计数
        $nonNullCount = $this->repository->count(['isoName' => '中国']);
        $this->assertIsInt($nonNullCount);
        $this->assertGreaterThanOrEqual(1, $nonNullCount);
    }

    private static int $phoneCounter = 0;

    private function generateUniquePhoneNumber(string $prefix = '138'): string
    {
        ++self::$phoneCounter;

        return $prefix . str_pad((string) (time() % 100000000 + self::$phoneCounter), 8, '0', STR_PAD_LEFT);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(PhoneNumberInfoRepository::class);
    }

    protected function createNewEntity(): object
    {
        $entity = new PhoneNumberInfo();
        // 使用唯一的手机号码生成器
        $uniqueNumber = $this->generateUniquePhoneNumber();
        $entity->setPhoneNumber($uniqueNumber);
        $entity->setNationCode('+86');

        return $entity;
    }

    protected function getRepository(): PhoneNumberInfoRepository
    {
        return $this->repository;
    }
}
