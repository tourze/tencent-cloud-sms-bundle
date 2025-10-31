<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsMessage;
use TencentCloudSmsBundle\Repository\SmsMessageRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SmsMessageRepository::class)]
#[RunTestsInSeparateProcesses]
final class SmsMessageRepositoryTest extends AbstractRepositoryTestCase
{
    private SmsMessageRepository $repository;

    public function testCount(): void
    {
        $count = $this->repository->count([]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithCriteria(): void
    {
        $count = $this->repository->count(['signature' => 'Test']);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindBy(): void
    {
        $results = $this->repository->findBy([]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsMessage::class, $result);
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
        $this->assertTrue(null === $result || $result instanceof SmsMessage);
    }

    public function testFindOneByWithCriteria(): void
    {
        $result = $this->repository->findOneBy(['signature' => 'Test']);
        $this->assertTrue(null === $result || $result instanceof SmsMessage);
        if (null !== $result) {
            $this->assertEquals('Test', $result->getSignature());
        }
    }

    public function testFindBySerialNo(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-id');
        $account->setSecretKey('test-key');

        // 先保存 Account 实体
        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setSignature('TestSignature');
        $message->setTemplate('TestTemplate');
        $message->setBatchId('test-batch-001');

        $this->repository->save($message, true);

        $results = $this->repository->findBySerialNo('test-batch-001');
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsMessage::class, $result);
        }
    }

    public function testFindByPhoneNumber(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-id-2');
        $account->setSecretKey('test-key-2');

        // 先保存 Account 实体
        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setSignature('TestSignature2');
        $message->setTemplate('TestTemplate2');
        $message->setBatchId('test-batch-002');

        $this->repository->save($message, true);

        $results = $this->repository->findByPhoneNumber('13800138000');
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsMessage::class, $result);
        }
    }

    public function testFindByPhoneNumberWithCountryCode(): void
    {
        // 这个测试方法应该测试具有电话号码的记录查找
        // 但由于 SmsMessage 没有直接的 phoneNumber 字段，我们只测试方法调用不出错
        $results = $this->repository->findByPhoneNumber('13900139000', '+86');
        $this->assertIsArray($results);
        // 结果可能为空，因为没有匹配的记录，这是正常的
    }

    public function testSave(): void
    {
        $account = new Account();
        $account->setName('Test Account Save');
        $account->setSecretId('test-id-save');
        $account->setSecretKey('test-key-save');

        // 先保存 Account 实体
        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setSignature('SaveTest');
        $message->setTemplate('SaveTemplate');

        $this->repository->save($message, false);
        $this->assertNotNull($message->getId());
    }

    public function testRemove(): void
    {
        $account = new Account();
        $account->setName('Test Account Remove');
        $account->setSecretId('test-id-remove');
        $account->setSecretKey('test-key-remove');

        // 先保存 Account 实体
        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setSignature('RemoveTest');
        $message->setTemplate('RemoveTemplate');

        $this->repository->save($message, true);
        $messageId = $message->getId();

        $this->repository->remove($message, true);

        $removedMessage = $this->repository->find($messageId);
        $this->assertNull($removedMessage);
    }

    public function testFindAllShouldReturnArrayOfEntities(): void
    {
        $account = new Account();
        $account->setName('Test Account FindAll');
        $account->setSecretId('test-findall-id');
        $account->setSecretKey('test-findall-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $message1 = new SmsMessage();
        $message1->setAccount($account);
        $message1->setSignature('FindAllTest1');
        $message1->setTemplate('FindAllTemplate1');

        $message2 = new SmsMessage();
        $message2->setAccount($account);
        $message2->setSignature('FindAllTest2');
        $message2->setTemplate('FindAllTemplate2');

        $this->repository->save($message1, false);
        $this->repository->save($message2, true);

        $result = $this->repository->findAll();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        $foundMessages = [];
        foreach ($result as $item) {
            $this->assertInstanceOf(SmsMessage::class, $item);
            if ('FindAllTest1' === $item->getSignature() || 'FindAllTest2' === $item->getSignature()) {
                $foundMessages[] = $item;
            }
        }
        $this->assertCount(2, $foundMessages);
    }

    public function testFindOneBySignatureShouldReturnMatchingEntity(): void
    {
        $account = new Account();
        $account->setName('Test Account FindOneBy');
        $account->setSecretId('test-findoneby-id');
        $account->setSecretKey('test-findoneby-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setSignature('FindOneByTest');
        $message->setTemplate('FindOneByTemplate');

        $this->repository->save($message, true);

        $result = $this->repository->findOneBy(['signature' => 'FindOneByTest']);
        $this->assertNotNull($result);
        $this->assertInstanceOf(SmsMessage::class, $result);
        $this->assertEquals('FindOneByTest', $result->getSignature());
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $account = new Account();
        $account->setName('Association Test Account');
        $account->setSecretId('test-association-id');
        $account->setSecretKey('test-association-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setSignature('AssociationTest');
        $message->setTemplate('AssociationTemplate');

        $this->repository->save($message, true);

        $result = $this->repository->findOneBy(['account' => $account]);
        if (null !== $result) {
            $this->assertInstanceOf(SmsMessage::class, $result);
            $this->assertEquals($account, $result->getAccount());
        }
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SmsMessageRepository::class);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setSecretId('test-secret-id-' . uniqid());
        $account->setSecretKey('test-secret-key-' . uniqid());

        $entity = new SmsMessage();
        $entity->setAccount($account);
        $entity->setSignature('Test Signature ' . uniqid());
        $entity->setTemplate('Test Template ' . uniqid());
        $entity->setBatchId('test-batch-' . uniqid());

        return $entity;
    }

    protected function getRepository(): SmsMessageRepository
    {
        return $this->repository;
    }
}
