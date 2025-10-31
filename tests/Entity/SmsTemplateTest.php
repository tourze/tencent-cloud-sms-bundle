<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\Enum\TemplateReviewStatus;
use TencentCloudSmsBundle\Enum\TemplateType;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(SmsTemplate::class)]
final class SmsTemplateTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): SmsTemplate
    {
        $template = new SmsTemplate();

        // 设置必需的属性以避免构造函数错误
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $template->setAccount($account);
        $template->setTemplateName('测试模板');
        $template->setTemplateContent('您的验证码是{code}，5分钟内有效。');

        return $template;
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'templateId' => ['templateId', 'template-123456'],
            'templateName' => ['templateName', '测试模板'],
            'templateContent' => ['templateContent', '您的验证码是{code}，5分钟内有效。'],
            'templateStatus' => ['templateStatus', TemplateReviewStatus::PENDING],
            'templateType' => ['templateType', TemplateType::NOTIFICATION],
            'international' => ['international', false],
            'remark' => ['remark', '测试备注'],
            'createTime' => ['createTime', new \DateTimeImmutable()],
            'updateTime' => ['updateTime', new \DateTimeImmutable()],
        ];
    }

    public function testSettersWork(): void
    {
        // 测试setter方法功能正常
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $entity = $this->createEntity();
        $entity->setTemplateId('template-123456');
        $entity->setTemplateName('测试模板');
        $entity->setTemplateContent('您的验证码是{code}，5分钟内有效。');
        $entity->setTemplateStatus(TemplateReviewStatus::PENDING);
        $entity->setTemplateType(TemplateType::NOTIFICATION);
        $entity->setInternational(false);
        $entity->setRemark('测试备注');

        // 验证值设置正确
        $this->assertSame('template-123456', $entity->getTemplateId());
        $this->assertSame('测试模板', $entity->getTemplateName());
        $this->assertSame(TemplateReviewStatus::PENDING, $entity->getTemplateStatus());
    }

    public function testCanBeInstantiated(): void
    {
        $entity = $this->createEntity();
        $this->assertNotNull($entity);
    }

    public function testImplementsStringable(): void
    {
        $entity = $this->createEntity();
        // Test that the string conversion works without error
        $stringValue = (string) $entity;
        $this->assertNotEmpty($stringValue);
    }

    public function testTemplateIdGetterSetter(): void
    {
        $entity = $this->createEntity();
        $templateId = 'template-123456';
        $entity->setTemplateId($templateId);
        $this->assertEquals($templateId, $entity->getTemplateId());
    }

    public function testTemplateNameGetterSetter(): void
    {
        $entity = $this->createEntity();
        $templateName = '测试模板';
        $entity->setTemplateName($templateName);
        $this->assertEquals($templateName, $entity->getTemplateName());
    }

    public function testTemplateContentGetterSetter(): void
    {
        $entity = $this->createEntity();
        $templateContent = '您的验证码是{code}，5分钟内有效。';
        $entity->setTemplateContent($templateContent);
        $this->assertEquals($templateContent, $entity->getTemplateContent());
    }

    public function testTemplateStatusGetterSetter(): void
    {
        $entity = $this->createEntity();
        $templateStatus = TemplateReviewStatus::PENDING;
        $entity->setTemplateStatus($templateStatus);
        $this->assertEquals($templateStatus, $entity->getTemplateStatus());
    }

    public function testTemplateTypeGetterSetter(): void
    {
        $entity = $this->createEntity();
        $templateType = TemplateType::NOTIFICATION;
        $entity->setTemplateType($templateType);
        $this->assertEquals($templateType, $entity->getTemplateType());
    }

    public function testInternationalGetterSetter(): void
    {
        $entity = $this->createEntity();
        $international = false;
        $entity->setInternational($international);
        $this->assertEquals($international, $entity->isInternational());
    }

    public function testRemarkGetterSetter(): void
    {
        $entity = $this->createEntity();
        $remark = '测试备注';
        $entity->setRemark($remark);
        $this->assertEquals($remark, $entity->getRemark());
    }

    public function testAccountGetterSetter(): void
    {
        $entity = $this->createEntity();
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $entity->setAccount($account);
        $this->assertSame($account, $entity->getAccount());
    }
}
