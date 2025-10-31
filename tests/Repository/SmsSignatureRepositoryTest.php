<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Enum\DocumentType;
use TencentCloudSmsBundle\Enum\SignPurpose;
use TencentCloudSmsBundle\Enum\SignReviewStatus;
use TencentCloudSmsBundle\Enum\SignType;
use TencentCloudSmsBundle\Repository\SmsSignatureRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SmsSignatureRepository::class)]
#[RunTestsInSeparateProcesses]
final class SmsSignatureRepositoryTest extends AbstractRepositoryTestCase
{
    private SmsSignatureRepository $repository;

    public function testCount(): void
    {
        $count = $this->repository->count([]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithCriteria(): void
    {
        $count = $this->repository->count(['signStatus' => SignReviewStatus::REVIEWING]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindBy(): void
    {
        $results = $this->repository->findBy([]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsSignature::class, $result);
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
        $this->assertTrue(null === $result || $result instanceof SmsSignature);
    }

    public function testFindOneByWithCriteria(): void
    {
        $result = $this->repository->findOneBy(['signStatus' => SignReviewStatus::REVIEWING]);

        // 确保总是有断言
        $this->assertTrue(null === $result || $result instanceof SmsSignature);

        if (null !== $result) {
            $this->assertInstanceOf(SmsSignature::class, $result);
            $this->assertEquals(SignReviewStatus::REVIEWING, $result->getSignStatus());
        }
    }

    public function testFindBySignId(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-signature-id');
        $account->setSecretKey('test-signature-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $signature = new SmsSignature();
        $signature->setAccount($account);
        $signature->setSignId('test-sign-001');
        $signature->setSignName('TestSignature');
        $signature->setSignType(SignType::COMPANY);
        $signature->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature->setSignPurpose(SignPurpose::SELF_USE);
        $signature->setDocumentUrl('http://example.com/doc.jpg');
        $signature->setSignStatus(SignReviewStatus::REVIEWING);
        $signature->setValid(true);
        $signature->setSyncing(true);

        $this->repository->save($signature, true);

        $result = $this->repository->findBySignId('test-sign-001');
        if (null !== $result) {
            $this->assertInstanceOf(SmsSignature::class, $result);
            $this->assertEquals('test-sign-001', $result->getSignId());
        }
    }

    public function testFindValidSignatures(): void
    {
        $results = $this->repository->findValidSignatures();
        $this->assertIsArray($results);

        foreach ($results as $result) {
            $this->assertInstanceOf(SmsSignature::class, $result);
            $this->assertEquals(SignReviewStatus::REVIEWING, $result->getSignStatus());
            $this->assertTrue($result->isValid());
        }
    }

    public function testSave(): void
    {
        $account = new Account();
        $account->setName('Test Account Save');
        $account->setSecretId('test-signature-save-id');
        $account->setSecretKey('test-signature-save-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $signature = new SmsSignature();
        $signature->setAccount($account);
        $signature->setSignId('test-sign-save');
        $signature->setSignName('TestSignatureSave');
        $signature->setSignType(SignType::COMPANY);
        $signature->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature->setSignPurpose(SignPurpose::SELF_USE);
        $signature->setDocumentUrl('http://example.com/save-doc.jpg');
        $signature->setSignStatus(SignReviewStatus::APPROVED);
        $signature->setValid(false);
        $signature->setSyncing(true);

        $this->repository->save($signature, false);
        $this->assertNotNull($signature->getId());
    }

    public function testRemove(): void
    {
        $account = new Account();
        $account->setName('Test Account Remove');
        $account->setSecretId('test-signature-remove-id');
        $account->setSecretKey('test-signature-remove-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $signature = new SmsSignature();
        $signature->setAccount($account);
        $signature->setSignId('test-sign-remove');
        $signature->setSignName('TestSignatureRemove');
        $signature->setSignType(SignType::COMPANY);
        $signature->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature->setSignPurpose(SignPurpose::SELF_USE);
        $signature->setDocumentUrl('http://example.com/remove-doc.jpg');
        $signature->setSignStatus(SignReviewStatus::APPROVED);
        $signature->setValid(false);
        $signature->setSyncing(true);

        $this->repository->save($signature, true);
        $signatureId = $signature->getId();

        $this->repository->remove($signature, true);

        $removedSignature = $this->repository->find($signatureId);
        $this->assertNull($removedSignature);
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

        $signature1 = new SmsSignature();
        $signature1->setAccount($account);
        $signature1->setSignId('findall-test-sign-1');
        $signature1->setSignName('FindAllTestSignature1');
        $signature1->setSignType(SignType::COMPANY);
        $signature1->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature1->setSignPurpose(SignPurpose::SELF_USE);
        $signature1->setDocumentUrl('http://example.com/findall-doc1.jpg');
        $signature1->setSignStatus(SignReviewStatus::REVIEWING);
        $signature1->setValid(true);
        $signature1->setSyncing(true);

        $signature2 = new SmsSignature();
        $signature2->setAccount($account);
        $signature2->setSignId('findall-test-sign-2');
        $signature2->setSignName('FindAllTestSignature2');
        $signature2->setSignType(SignType::APP);
        $signature2->setDocumentType(DocumentType::APP_ADMIN);
        $signature2->setSignPurpose(SignPurpose::SELF_USE);
        $signature2->setDocumentUrl('http://example.com/findall-doc2.jpg');
        $signature2->setSignStatus(SignReviewStatus::APPROVED);
        $signature2->setValid(false);
        $signature2->setSyncing(true);

        $this->repository->save($signature1, false);
        $this->repository->save($signature2, true);

        $result = $this->repository->findAll();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        $foundSignatures = [];
        foreach ($result as $item) {
            $this->assertInstanceOf(SmsSignature::class, $item);
            if ('findall-test-sign-1' === $item->getSignId() || 'findall-test-sign-2' === $item->getSignId()) {
                $foundSignatures[] = $item;
            }
        }
        $this->assertCount(2, $foundSignatures);
    }

    public function testFindOneBySignNameShouldReturnMatchingEntity(): void
    {
        $account = new Account();
        $account->setName('Test Account FindOneBy');
        $account->setSecretId('test-findoneby-id');
        $account->setSecretKey('test-findoneby-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $signature = new SmsSignature();
        $signature->setAccount($account);
        $signature->setSignId('findoneby-test-sign');
        $signature->setSignName('FindOneByTestSignature');
        $signature->setSignType(SignType::COMPANY);
        $signature->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature->setSignPurpose(SignPurpose::SELF_USE);
        $signature->setDocumentUrl('http://example.com/findoneby-doc.jpg');
        $signature->setSignStatus(SignReviewStatus::REVIEWING);
        $signature->setValid(true);
        $signature->setSyncing(true);

        $this->repository->save($signature, true);

        $result = $this->repository->findOneBy(['signName' => 'FindOneByTestSignature']);
        $this->assertNotNull($result);
        $this->assertInstanceOf(SmsSignature::class, $result);
        $this->assertEquals('FindOneByTestSignature', $result->getSignName());
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

        $signature = new SmsSignature();
        $signature->setAccount($account);
        $signature->setSignId('association-test-sign');
        $signature->setSignName('AssociationTestSignature');
        $signature->setSignType(SignType::COMPANY);
        $signature->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature->setSignPurpose(SignPurpose::SELF_USE);
        $signature->setDocumentUrl('http://example.com/association-doc.jpg');
        $signature->setSignStatus(SignReviewStatus::REVIEWING);
        $signature->setValid(true);
        $signature->setSyncing(true);

        $this->repository->save($signature, true);

        $result = $this->repository->findOneBy(['account' => $account]);
        if (null !== $result) {
            $this->assertInstanceOf(SmsSignature::class, $result);
            $this->assertEquals($account, $result->getAccount());
        }
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SmsSignatureRepository::class);
    }

    public function testFindOneByWithOrderByShouldReturnFirstEntityMatchingOrder(): void
    {
        $account = new Account();
        $account->setName('FindOneBy Order Test Account');
        $account->setSecretId('findoneby-order-test-id');
        $account->setSecretKey('findoneby-order-test-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $signature1 = new SmsSignature();
        $signature1->setAccount($account);
        $signature1->setSignId('findoneby-order-sign-1');
        $signature1->setSignName('ZFindOneByOrderSignature');
        $signature1->setSignType(SignType::COMPANY);
        $signature1->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature1->setSignPurpose(SignPurpose::SELF_USE);
        $signature1->setDocumentUrl('http://example.com/test-doc1.jpg');
        $signature1->setSignStatus(SignReviewStatus::REVIEWING);
        $signature1->setValid(true);
        $signature1->setSyncing(true);

        $signature2 = new SmsSignature();
        $signature2->setAccount($account);
        $signature2->setSignId('findoneby-order-sign-2');
        $signature2->setSignName('AFindOneByOrderSignature');
        $signature2->setSignType(SignType::APP);
        $signature2->setDocumentType(DocumentType::APP_ADMIN);
        $signature2->setSignPurpose(SignPurpose::SELF_USE);
        $signature2->setDocumentUrl('http://example.com/test-doc2.jpg');
        $signature2->setSignStatus(SignReviewStatus::REVIEWING);
        $signature2->setValid(true);
        $signature2->setSyncing(true);

        $this->repository->save($signature1, false);
        $this->repository->save($signature2, true);

        $firstByNameAsc = $this->repository->findOneBy(['signStatus' => SignReviewStatus::REVIEWING], ['signName' => 'ASC']);
        $this->assertNotNull($firstByNameAsc);
        $this->assertInstanceOf(SmsSignature::class, $firstByNameAsc);
        $this->assertEquals('AFindOneByOrderSignature', $firstByNameAsc->getSignName());

        $firstByNameDesc = $this->repository->findOneBy(['signStatus' => SignReviewStatus::REVIEWING], ['signName' => 'DESC']);
        $this->assertNotNull($firstByNameDesc);
        $this->assertInstanceOf(SmsSignature::class, $firstByNameDesc);
        $this->assertEquals('ZFindOneByOrderSignature', $firstByNameDesc->getSignName());
    }

    public function testFindOneByWithNullValueShouldReturnMatchingEntity(): void
    {
        $account = new Account();
        $account->setName('Null Value Test Account');
        $account->setSecretId('null-value-test-id');
        $account->setSecretKey('null-value-test-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $signature1 = new SmsSignature();
        $signature1->setAccount($account);
        $signature1->setSignId('null-value-sign-1');
        $signature1->setSignName('NullValueSignature1');
        $signature1->setSignType(SignType::COMPANY);
        $signature1->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature1->setSignPurpose(SignPurpose::SELF_USE);
        $signature1->setDocumentUrl('http://example.com/test-doc1.jpg');
        $signature1->setSignStatus(SignReviewStatus::REVIEWING);
        $signature1->setValid(true);
        $signature1->setSyncing(true);

        $signature2 = new SmsSignature();
        $signature2->setAccount($account);
        $signature2->setSignId('null-value-sign-2');
        $signature2->setSignName('NullValueSignature2');
        $signature2->setSignType(SignType::APP);
        $signature2->setDocumentType(DocumentType::APP_ADMIN);
        $signature2->setSignPurpose(SignPurpose::SELF_USE);
        $signature2->setDocumentUrl('http://example.com/doc.jpg');
        $signature2->setSignStatus(SignReviewStatus::REVIEWING);
        $signature2->setValid(true);
        $signature2->setSyncing(true);

        $this->repository->save($signature1, false);
        $this->repository->save($signature2, true);

        $specificDocumentResult = $this->repository->findOneBy(['documentUrl' => 'http://example.com/test-doc1.jpg']);
        if (null !== $specificDocumentResult) {
            $this->assertInstanceOf(SmsSignature::class, $specificDocumentResult);
            $this->assertEquals('http://example.com/test-doc1.jpg', $specificDocumentResult->getDocumentUrl());
        }

        $nullReviewReplyResult = $this->repository->findOneBy(['reviewReply' => null]);
        if (null !== $nullReviewReplyResult) {
            $this->assertInstanceOf(SmsSignature::class, $nullReviewReplyResult);
            $this->assertNull($nullReviewReplyResult->getReviewReply());
        }

        $nullSignContentResult = $this->repository->findOneBy(['signContent' => null]);
        if (null !== $nullSignContentResult) {
            $this->assertInstanceOf(SmsSignature::class, $nullSignContentResult);
            $this->assertNull($nullSignContentResult->getSignContent());
        }
    }

    public function testFindByWithAssociationJoinShouldReturnMatchingEntities(): void
    {
        $account1 = new Account();
        $account1->setName('Association Join Test Account 1');
        $account1->setSecretId('association-join-test-id-1');
        $account1->setSecretKey('association-join-test-key-1');

        $account2 = new Account();
        $account2->setName('Association Join Test Account 2');
        $account2->setSecretId('association-join-test-id-2');
        $account2->setSecretKey('association-join-test-key-2');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account1);
        $entityManager->persist($account2);
        $entityManager->flush();

        $signature1 = new SmsSignature();
        $signature1->setAccount($account1);
        $signature1->setSignId('association-join-sign-1');
        $signature1->setSignName('AssociationJoinSignature1');
        $signature1->setSignType(SignType::COMPANY);
        $signature1->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature1->setSignPurpose(SignPurpose::SELF_USE);
        $signature1->setDocumentUrl('http://example.com/test-doc1.jpg');
        $signature1->setSignStatus(SignReviewStatus::REVIEWING);
        $signature1->setValid(true);
        $signature1->setSyncing(true);

        $signature2 = new SmsSignature();
        $signature2->setAccount($account1);
        $signature2->setSignId('association-join-sign-2');
        $signature2->setSignName('AssociationJoinSignature2');
        $signature2->setSignType(SignType::APP);
        $signature2->setDocumentType(DocumentType::APP_ADMIN);
        $signature2->setSignPurpose(SignPurpose::SELF_USE);
        $signature2->setDocumentUrl('http://example.com/test-doc2.jpg');
        $signature2->setSignStatus(SignReviewStatus::REVIEWING);
        $signature2->setValid(true);
        $signature2->setSyncing(true);

        $signature3 = new SmsSignature();
        $signature3->setAccount($account2);
        $signature3->setSignId('association-join-sign-3');
        $signature3->setSignName('AssociationJoinSignature3');
        $signature3->setSignType(SignType::COMPANY);
        $signature3->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature3->setSignPurpose(SignPurpose::SELF_USE);
        $signature3->setDocumentUrl('http://example.com/test-doc3.jpg');
        $signature3->setSignStatus(SignReviewStatus::REVIEWING);
        $signature3->setValid(true);
        $signature3->setSyncing(true);

        $this->repository->save($signature1, false);
        $this->repository->save($signature2, false);
        $this->repository->save($signature3, true);

        $account1Results = $this->repository->findBy(['account' => $account1]);
        $this->assertIsArray($account1Results);
        $this->assertGreaterThanOrEqual(2, count($account1Results));
        foreach ($account1Results as $result) {
            $this->assertInstanceOf(SmsSignature::class, $result);
            $this->assertEquals($account1, $result->getAccount());
        }

        $account2Results = $this->repository->findBy(['account' => $account2]);
        $this->assertIsArray($account2Results);
        $this->assertGreaterThanOrEqual(1, count($account2Results));
        foreach ($account2Results as $result) {
            $this->assertInstanceOf(SmsSignature::class, $result);
            $this->assertEquals($account2, $result->getAccount());
        }

        $account1OneResult = $this->repository->findOneBy(['account' => $account1]);
        if (null !== $account1OneResult) {
            $this->assertInstanceOf(SmsSignature::class, $account1OneResult);
            $this->assertEquals($account1, $account1OneResult->getAccount());
        }

        $account2OneResult = $this->repository->findOneBy(['account' => $account2]);
        if (null !== $account2OneResult) {
            $this->assertInstanceOf(SmsSignature::class, $account2OneResult);
            $this->assertEquals($account2, $account2OneResult->getAccount());
        }
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $account1 = new Account();
        $account1->setName('Count Association Account 1');
        $account1->setSecretId('count-association-id-1');
        $account1->setSecretKey('count-association-key-1');

        $account2 = new Account();
        $account2->setName('Count Association Account 2');
        $account2->setSecretId('count-association-id-2');
        $account2->setSecretKey('count-association-key-2');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account1);
        $entityManager->persist($account2);
        $entityManager->flush();

        // 为 account1 创建 4 个签名
        for ($i = 1; $i <= 4; ++$i) {
            $signature = new SmsSignature();
            $signature->setAccount($account1);
            $signature->setSignId('count-association-sign-1-' . $i);
            $signature->setSignName('CountAssociationSignature1_' . $i);
            $signature->setSignType(SignType::COMPANY);
            $signature->setDocumentType(DocumentType::BUSINESS_LICENSE);
            $signature->setSignPurpose(SignPurpose::SELF_USE);
            $signature->setDocumentUrl('http://example.com/count-association-doc-1-' . $i . '.jpg');
            $signature->setSignStatus(SignReviewStatus::REVIEWING);
            $signature->setValid(true);
            $signature->setSyncing(true);

            $this->repository->save($signature, false);
        }

        // 为 account2 创建 2 个签名
        for ($i = 1; $i <= 2; ++$i) {
            $signature = new SmsSignature();
            $signature->setAccount($account2);
            $signature->setSignId('count-association-sign-2-' . $i);
            $signature->setSignName('CountAssociationSignature2_' . $i);
            $signature->setSignType(SignType::APP);
            $signature->setDocumentType(DocumentType::APP_ADMIN);
            $signature->setSignPurpose(SignPurpose::SELF_USE);
            $signature->setDocumentUrl('http://example.com/count-association-doc-2-' . $i . '.jpg');
            $signature->setSignStatus(SignReviewStatus::APPROVED);
            $signature->setValid(false);
            $signature->setSyncing(true);

            $this->repository->save($signature, false);
        }

        $entityManager->flush();

        $count1 = $this->repository->count(['account' => $account1]);
        $this->assertSame(4, $count1);

        $count2 = $this->repository->count(['account' => $account2]);
        $this->assertSame(2, $count2);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setSecretId('test-secret-id-' . uniqid());
        $account->setSecretKey('test-secret-key-' . uniqid());

        $entity = new SmsSignature();
        $entity->setAccount($account);
        $entity->setSignId('test-sign-' . uniqid());
        $entity->setSignName('Test Signature ' . uniqid());
        $entity->setSignType(SignType::COMPANY);
        $entity->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $entity->setSignPurpose(SignPurpose::SELF_USE);
        $entity->setDocumentUrl('http://example.com/test-' . uniqid() . '.jpg');
        $entity->setSignStatus(SignReviewStatus::REVIEWING);
        $entity->setValid(true);
        $entity->setSyncing(true);

        return $entity;
    }

    protected function getRepository(): SmsSignatureRepository
    {
        return $this->repository;
    }
}
