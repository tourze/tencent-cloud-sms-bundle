<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Entity\SmsMessage;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\SendStatus;
use TencentCloudSmsBundle\Repository\SmsRecipientRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SmsRecipientRepository::class)]
#[RunTestsInSeparateProcesses]
final class SmsRecipientRepositoryTest extends AbstractRepositoryTestCase
{
    private SmsRecipientRepository $repository;

    public function testCount(): void
    {
        $count = $this->repository->count([]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithCriteria(): void
    {
        $count = $this->repository->count(['serialNo' => '123456']);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindBy(): void
    {
        $results = $this->repository->findBy([]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsRecipient::class, $result);
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
        $this->assertTrue(null === $result || $result instanceof SmsRecipient);
    }

    public function testFindOneByWithCriteria(): void
    {
        $result = $this->repository->findOneBy(['serialNo' => '123456']);
        $this->assertTrue(null === $result || $result instanceof SmsRecipient);
        if (null !== $result) {
            $this->assertEquals('123456', $result->getSerialNo());
        }
    }

    public function testFindNeedSyncStatus(): void
    {
        $sendAfter = new \DateTime('-1 hour');
        $results = $this->repository->findNeedSyncStatus($sendAfter);

        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsRecipient::class, $result);
            if (null !== $result->getSendTime()) {
                $this->assertGreaterThanOrEqual($sendAfter, $result->getSendTime());
            }
        }
    }

    public function testFindUnknownStatus(): void
    {
        $results = $this->repository->findUnknownStatus();
        $this->assertIsArray($results);
        $this->assertLessThanOrEqual(100, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(SmsRecipient::class, $result);
        }
    }

    public function testFindUnknownStatusWithLimit(): void
    {
        $results = $this->repository->findUnknownStatus(10);
        $this->assertIsArray($results);
        $this->assertLessThanOrEqual(10, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(SmsRecipient::class, $result);
        }
    }

    public function testSave(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-id-recipient');
        $account->setSecretKey('test-key-recipient');

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setSignature('TestSig');
        $message->setTemplate('TestTpl');

        $phoneInfo = new PhoneNumberInfo();
        $phoneInfo->setPhoneNumber('13500' . uniqid());
        $phoneInfo->setNationCode('+86');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->persist($message);
        $entityManager->persist($phoneInfo);
        $entityManager->flush();

        $recipient = new SmsRecipient();
        $recipient->setMessage($message);
        $recipient->setPhoneNumber($phoneInfo);
        $recipient->setStatus(SendStatus::SUCCESS);
        $recipient->setSerialNo('test-serial-001');

        $this->repository->save($recipient, false);
        $this->assertNotNull($recipient->getId());
    }

    public function testRemove(): void
    {
        $account = new Account();
        $account->setName('Test Account Remove');
        $account->setSecretId('test-id-recipient-remove');
        $account->setSecretKey('test-key-recipient-remove');

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setSignature('TestSigRemove');
        $message->setTemplate('TestTplRemove');

        $phoneInfo = new PhoneNumberInfo();
        $phoneInfo->setPhoneNumber('13400' . uniqid());
        $phoneInfo->setNationCode('+86');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->persist($message);
        $entityManager->persist($phoneInfo);
        $entityManager->flush();

        $recipient = new SmsRecipient();
        $recipient->setMessage($message);
        $recipient->setPhoneNumber($phoneInfo);
        $recipient->setStatus(SendStatus::SUCCESS);
        $recipient->setSerialNo('test-serial-remove');

        $this->repository->save($recipient, true);
        $recipientId = $recipient->getId();

        $this->repository->remove($recipient, true);

        $removedRecipient = $this->repository->find($recipientId);
        $this->assertNull($removedRecipient);
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SmsRecipientRepository::class);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setSecretId('test-secret-id-' . uniqid());
        $account->setSecretKey('test-secret-key-' . uniqid());

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setSignature('Test Signature ' . uniqid());
        $message->setTemplate('Test Template ' . uniqid());
        $message->setBatchId('test-batch-' . uniqid());

        $phoneInfo = new PhoneNumberInfo();
        $phoneInfo->setPhoneNumber('13800' . uniqid());
        $phoneInfo->setNationCode('+86');

        $entity = new SmsRecipient();
        $entity->setMessage($message);
        $entity->setPhoneNumber($phoneInfo);
        $entity->setStatus(SendStatus::SUCCESS);
        $entity->setSerialNo('test-serial-' . uniqid());

        return $entity;
    }

    protected function getRepository(): SmsRecipientRepository
    {
        return $this->repository;
    }
}
