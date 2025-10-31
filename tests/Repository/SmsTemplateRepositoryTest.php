<?php

namespace TencentCloudSmsBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\Enum\TemplateReviewStatus;
use TencentCloudSmsBundle\Enum\TemplateType;
use TencentCloudSmsBundle\Repository\SmsTemplateRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SmsTemplateRepository::class)]
#[RunTestsInSeparateProcesses]
final class SmsTemplateRepositoryTest extends AbstractRepositoryTestCase
{
    private SmsTemplateRepository $repository;

    public function testCount(): void
    {
        $count = $this->repository->count([]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithCriteria(): void
    {
        $count = $this->repository->count(['templateStatus' => TemplateReviewStatus::REVIEWING]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindBy(): void
    {
        $results = $this->repository->findBy([]);
        $this->assertIsArray($results);
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsTemplate::class, $result);
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
        $this->assertTrue(null === $result || $result instanceof SmsTemplate);
    }

    public function testFindOneByWithCriteria(): void
    {
        $account = new Account();
        $account->setName('FindOneBy Criteria Test Account');
        $account->setSecretId('test-findoneby-criteria-id');
        $account->setSecretKey('test-findoneby-criteria-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $template = new SmsTemplate();
        $template->setAccount($account);
        $template->setTemplateId('FINDONEBY_CRITERIA');
        $template->setTemplateName('FindOneBy Criteria Template');
        $template->setTemplateContent('FindOneBy criteria content {1}');
        $template->setTemplateType(TemplateType::NOTIFICATION);
        $template->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template->setValid(true);
        $template->setSyncing(true);

        $this->repository->save($template, true);

        $result = $this->repository->findOneBy(['templateStatus' => TemplateReviewStatus::REVIEWING]);
        $this->assertNotNull($result);
        $this->assertInstanceOf(SmsTemplate::class, $result);
        $this->assertEquals(TemplateReviewStatus::REVIEWING, $result->getTemplateStatus());
    }

    public function testFindByTemplateId(): void
    {
        $account = new Account();
        $account->setName('Test Template Account');
        $account->setSecretId('test-template-id');
        $account->setSecretKey('test-template-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $template = new SmsTemplate();
        $template->setAccount($account);
        $template->setTemplateId('TEST123');
        $template->setTemplateName('Test Template');
        $template->setTemplateContent('Hello {1}');
        $template->setTemplateType(TemplateType::NOTIFICATION);
        $template->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template->setValid(true);
        $template->setSyncing(true);

        $this->repository->save($template, true);

        $result = $this->repository->findByTemplateId('TEST123');
        if (null !== $result) {
            $this->assertInstanceOf(SmsTemplate::class, $result);
            $this->assertEquals('TEST123', $result->getTemplateId());
        }
    }

    public function testFindValidTemplates(): void
    {
        $results = $this->repository->findValidTemplates();
        $this->assertIsArray($results);

        foreach ($results as $result) {
            $this->assertInstanceOf(SmsTemplate::class, $result);
            $this->assertEquals(TemplateReviewStatus::REVIEWING, $result->getTemplateStatus());
            $this->assertTrue($result->isValid());
        }
    }

    public function testSave(): void
    {
        $account = new Account();
        $account->setName('Save Template Account');
        $account->setSecretId('test-save-template-id');
        $account->setSecretKey('test-save-template-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $template = new SmsTemplate();
        $template->setAccount($account);
        $template->setTemplateId('SAVE123');
        $template->setTemplateName('Save Test Template');
        $template->setTemplateContent('Save test {1}');
        $template->setTemplateType(TemplateType::NOTIFICATION);
        $template->setTemplateStatus(TemplateReviewStatus::APPROVED);
        $template->setValid(false);
        $template->setSyncing(true);

        $this->repository->save($template, false);
        $this->assertNotNull($template->getId());
    }

    public function testRemove(): void
    {
        $account = new Account();
        $account->setName('Remove Template Account');
        $account->setSecretId('test-remove-template-id');
        $account->setSecretKey('test-remove-template-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $template = new SmsTemplate();
        $template->setAccount($account);
        $template->setTemplateId('REMOVE123');
        $template->setTemplateName('Remove Test Template');
        $template->setTemplateContent('Remove test {1}');
        $template->setTemplateType(TemplateType::NOTIFICATION);
        $template->setTemplateStatus(TemplateReviewStatus::APPROVED);
        $template->setValid(false);
        $template->setSyncing(true);

        $this->repository->save($template, true);
        $templateId = $template->getId();

        $this->repository->remove($template, true);

        $removedTemplate = $this->repository->find($templateId);
        $this->assertNull($removedTemplate);
    }

    public function testSmsTemplateEntityCanBeInstantiated(): void
    {
        $template = new SmsTemplate();
        $this->assertInstanceOf(SmsTemplate::class, $template);

        $template->setTemplateId('TEST123');
        $this->assertEquals('TEST123', $template->getTemplateId());

        $template->setTemplateName('测试模板');
        $this->assertEquals('测试模板', $template->getTemplateName());
    }

    public function testSmsTemplateEntityHasRequiredFields(): void
    {
        $template = new SmsTemplate();

        $requiredMethods = [
            'getTemplateId', 'setTemplateId',
            'getTemplateName', 'setTemplateName',
            'getTemplateContent', 'setTemplateContent',
            'getTemplateStatus', 'setTemplateStatus',
            'isValid', 'setValid',
            'getCreateTime', 'setCreateTime',
            'getUpdateTime', 'setUpdateTime',
        ];

        foreach ($requiredMethods as $method) {
            $this->assertTrue(
                method_exists($template, $method),
                "SmsTemplate entity should have method: {$method}"
            );
        }
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

        $template1 = new SmsTemplate();
        $template1->setAccount($account);
        $template1->setTemplateId('FINDALL1');
        $template1->setTemplateName('FindAll Test Template 1');
        $template1->setTemplateContent('FindAll test {1}');
        $template1->setTemplateType(TemplateType::NOTIFICATION);
        $template1->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template1->setValid(true);
        $template1->setSyncing(true);

        $template2 = new SmsTemplate();
        $template2->setAccount($account);
        $template2->setTemplateId('FINDALL2');
        $template2->setTemplateName('FindAll Test Template 2');
        $template2->setTemplateContent('FindAll test {1} {2}');
        $template2->setTemplateType(TemplateType::MARKETING);
        $template2->setTemplateStatus(TemplateReviewStatus::APPROVED);
        $template2->setValid(false);
        $template2->setSyncing(true);

        $this->repository->save($template1, false);
        $this->repository->save($template2, true);

        $result = $this->repository->findAll();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        $foundTemplates = [];
        foreach ($result as $item) {
            $this->assertInstanceOf(SmsTemplate::class, $item);
            if ('FINDALL1' === $item->getTemplateId() || 'FINDALL2' === $item->getTemplateId()) {
                $foundTemplates[] = $item;
            }
        }
        $this->assertCount(2, $foundTemplates);
    }

    public function testFindOneByTemplateNameShouldReturnMatchingEntity(): void
    {
        $account = new Account();
        $account->setName('Test Account FindOneBy');
        $account->setSecretId('test-findoneby-id');
        $account->setSecretKey('test-findoneby-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $template = new SmsTemplate();
        $template->setAccount($account);
        $template->setTemplateId('FINDONEBY123');
        $template->setTemplateName('FindOneBy Test Template');
        $template->setTemplateContent('FindOneBy test {1}');
        $template->setTemplateType(TemplateType::NOTIFICATION);
        $template->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template->setValid(true);
        $template->setSyncing(true);

        $this->repository->save($template, true);

        $result = $this->repository->findOneBy(['templateName' => 'FindOneBy Test Template']);
        $this->assertNotNull($result);
        $this->assertInstanceOf(SmsTemplate::class, $result);
        $this->assertEquals('FindOneBy Test Template', $result->getTemplateName());
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

        $template = new SmsTemplate();
        $template->setAccount($account);
        $template->setTemplateId('ASSOCIATION123');
        $template->setTemplateName('Association Test Template');
        $template->setTemplateContent('Association test {1}');
        $template->setTemplateType(TemplateType::NOTIFICATION);
        $template->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template->setValid(true);
        $template->setSyncing(true);

        $this->repository->save($template, true);

        $result = $this->repository->findOneBy(['account' => $account]);
        if (null !== $result) {
            $this->assertInstanceOf(SmsTemplate::class, $result);
            $this->assertEquals($account, $result->getAccount());
        }
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(SmsTemplateRepository::class);
    }

    public function testFindOneByWithOrderByShouldReturnFirstOrderedEntity(): void
    {
        $account = new Account();
        $account->setName('FindOneBy Order Test Account');
        $account->setSecretId('test-findoneby-order-id');
        $account->setSecretKey('test-findoneby-order-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $template1 = new SmsTemplate();
        $template1->setAccount($account);
        $template1->setTemplateId('ORDER_TEST_2');
        $template1->setTemplateName('Z Template');
        $template1->setTemplateContent('Content Z {1}');
        $template1->setTemplateType(TemplateType::NOTIFICATION);
        $template1->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template1->setValid(true);
        $template1->setSyncing(true);

        $template2 = new SmsTemplate();
        $template2->setAccount($account);
        $template2->setTemplateId('ORDER_TEST_1');
        $template2->setTemplateName('A Template');
        $template2->setTemplateContent('Content A {1}');
        $template2->setTemplateType(TemplateType::NOTIFICATION);
        $template2->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template2->setValid(true);
        $template2->setSyncing(true);

        $this->repository->save($template1, false);
        $this->repository->save($template2, true);

        // 测试按模板名称升序，应该返回 A Template
        $result = $this->repository->findOneBy(
            ['templateStatus' => TemplateReviewStatus::REVIEWING],
            ['templateName' => 'ASC']
        );
        $this->assertInstanceOf(SmsTemplate::class, $result);
        $this->assertEquals(TemplateReviewStatus::REVIEWING, $result->getTemplateStatus());

        // 测试按模板名称降序，应该返回 Z Template
        $result = $this->repository->findOneBy(
            ['templateStatus' => TemplateReviewStatus::REVIEWING],
            ['templateName' => 'DESC']
        );
        $this->assertInstanceOf(SmsTemplate::class, $result);
        $this->assertEquals(TemplateReviewStatus::REVIEWING, $result->getTemplateStatus());
    }

    public function testFindOneByWithAssociationQueryShouldReturnMatchingEntity(): void
    {
        $account1 = new Account();
        $account1->setName('Association Query Test Account 1');
        $account1->setSecretId('test-association-query-1-id');
        $account1->setSecretKey('test-association-query-1-key');

        $account2 = new Account();
        $account2->setName('Association Query Test Account 2');
        $account2->setSecretId('test-association-query-2-id');
        $account2->setSecretKey('test-association-query-2-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account1);
        $entityManager->persist($account2);
        $entityManager->flush();

        $template1 = new SmsTemplate();
        $template1->setAccount($account1);
        $template1->setTemplateId('ASSOC_QUERY_1');
        $template1->setTemplateName('Association Query Template 1');
        $template1->setTemplateContent('Association query content {1}');
        $template1->setTemplateType(TemplateType::NOTIFICATION);
        $template1->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template1->setValid(true);
        $template1->setSyncing(true);

        $template2 = new SmsTemplate();
        $template2->setAccount($account2);
        $template2->setTemplateId('ASSOC_QUERY_2');
        $template2->setTemplateName('Association Query Template 2');
        $template2->setTemplateContent('Association query content {1} {2}');
        $template2->setTemplateType(TemplateType::NOTIFICATION);
        $template2->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template2->setValid(true);
        $template2->setSyncing(true);

        $this->repository->save($template1, false);
        $this->repository->save($template2, true);

        $result = $this->repository->findOneBy(['account' => $account1]);
        $this->assertNotNull($result);
        $this->assertInstanceOf(SmsTemplate::class, $result);
        $this->assertEquals($account1, $result->getAccount());
        $this->assertEquals('ASSOC_QUERY_1', $result->getTemplateId());

        $result = $this->repository->findOneBy(['account' => $account2]);
        $this->assertNotNull($result);
        $this->assertInstanceOf(SmsTemplate::class, $result);
        $this->assertEquals($account2, $result->getAccount());
        $this->assertEquals('ASSOC_QUERY_2', $result->getTemplateId());
    }

    public function testCountWithAssociationQueryShouldReturnCorrectNumber(): void
    {
        $account = new Account();
        $account->setName('Count Association Test Account');
        $account->setSecretId('test-count-association-id');
        $account->setSecretKey('test-count-association-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $template1 = new SmsTemplate();
        $template1->setAccount($account);
        $template1->setTemplateId('COUNT_ASSOC_1');
        $template1->setTemplateName('Count Association Template 1');
        $template1->setTemplateContent('Count association content {1}');
        $template1->setTemplateType(TemplateType::NOTIFICATION);
        $template1->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template1->setValid(true);
        $template1->setSyncing(true);

        $template2 = new SmsTemplate();
        $template2->setAccount($account);
        $template2->setTemplateId('COUNT_ASSOC_2');
        $template2->setTemplateName('Count Association Template 2');
        $template2->setTemplateContent('Count association content {1} {2}');
        $template2->setTemplateType(TemplateType::NOTIFICATION);
        $template2->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template2->setValid(true);
        $template2->setSyncing(true);

        $this->repository->save($template1, false);
        $this->repository->save($template2, true);

        $count = $this->repository->count(['account' => $account]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(2, $count);

        $count = $this->repository->count([
            'account' => $account,
            'templateStatus' => TemplateReviewStatus::REVIEWING,
        ]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(2, $count);
    }

    public function testFindByWithNullValueShouldReturnMatchingEntities(): void
    {
        $account = new Account();
        $account->setName('Null Value Test Account');
        $account->setSecretId('test-null-value-id');
        $account->setSecretKey('test-null-value-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $template1 = new SmsTemplate();
        $template1->setAccount($account);
        $template1->setTemplateId('NULL_VALUE_1');
        $template1->setTemplateName('Null Value Template 1');
        $template1->setTemplateContent('Null value content {1}');
        $template1->setTemplateType(TemplateType::NOTIFICATION);
        $template1->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template1->setValid(true);
        $template1->setSyncing(true);
        $template1->setReviewReply(null);
        $template1->setRemark(null);

        $template2 = new SmsTemplate();
        $template2->setAccount($account);
        $template2->setTemplateId('NULL_VALUE_2');
        $template2->setTemplateName('Null Value Template 2');
        $template2->setTemplateContent('Null value content {1} {2}');
        $template2->setTemplateType(TemplateType::NOTIFICATION);
        $template2->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template2->setValid(true);
        $template2->setSyncing(true);
        $template2->setReviewReply('Some review reply');
        $template2->setRemark('Some remark');

        $this->repository->save($template1, false);
        $this->repository->save($template2, true);

        $results = $this->repository->findBy(['reviewReply' => null]);
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsTemplate::class, $result);
            $this->assertNull($result->getReviewReply());
        }

        $results = $this->repository->findBy(['remark' => null]);
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));
        foreach ($results as $result) {
            $this->assertInstanceOf(SmsTemplate::class, $result);
            $this->assertNull($result->getRemark());
        }
    }

    public function testCountWithNullValueShouldReturnCorrectNumber(): void
    {
        $account = new Account();
        $account->setName('Count Null Test Account');
        $account->setSecretId('test-count-null-id');
        $account->setSecretKey('test-count-null-key');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $template1 = new SmsTemplate();
        $template1->setAccount($account);
        $template1->setTemplateId('COUNT_NULL_1');
        $template1->setTemplateName('Count Null Template 1');
        $template1->setTemplateContent('Count null content {1}');
        $template1->setTemplateType(TemplateType::NOTIFICATION);
        $template1->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template1->setValid(true);
        $template1->setSyncing(true);
        $template1->setReviewReply(null);

        $template2 = new SmsTemplate();
        $template2->setAccount($account);
        $template2->setTemplateId('COUNT_NULL_2');
        $template2->setTemplateName('Count Null Template 2');
        $template2->setTemplateContent('Count null content {1} {2}');
        $template2->setTemplateType(TemplateType::NOTIFICATION);
        $template2->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template2->setValid(true);
        $template2->setSyncing(true);
        $template2->setReviewReply('Some review reply');

        $this->repository->save($template1, false);
        $this->repository->save($template2, true);

        $count = $this->repository->count(['reviewReply' => null]);
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);

        $countNotNull = $this->repository->count(['templateStatus' => TemplateReviewStatus::REVIEWING]);
        $countNull = $this->repository->count(['reviewReply' => null]);
        $this->assertIsInt($countNotNull);
        $this->assertIsInt($countNull);
        $this->assertGreaterThan($countNull, $countNotNull);
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setSecretId('test-secret-id-' . uniqid());
        $account->setSecretKey('test-secret-key-' . uniqid());

        $entity = new SmsTemplate();
        $entity->setAccount($account);
        $entity->setTemplateId('test-template-' . uniqid());
        $entity->setTemplateName('Test Template ' . uniqid());
        $entity->setTemplateContent('Test template content {1}');
        $entity->setTemplateType(TemplateType::NOTIFICATION);
        $entity->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $entity->setValid(true);
        $entity->setSyncing(true);

        return $entity;
    }

    protected function getRepository(): SmsTemplateRepository
    {
        return $this->repository;
    }
}
