<?php

namespace TencentCloudSmsBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\Enum\TemplateReviewStatus;
use TencentCloudSmsBundle\Enum\TemplateType;
use TencentCloudSmsBundle\EventSubscriber\SmsTemplateListener;
use TencentCloudSmsBundle\Exception\TemplateException;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(SmsTemplateListener::class)]
#[RunTestsInSeparateProcesses]
final class SmsTemplateListenerTest extends AbstractEventSubscriberTestCase
{
    private SmsTemplateListener $listener;

    protected function onSetUp(): void
    {
        // 通过容器获取服务
        $this->listener = self::getService(SmsTemplateListener::class);
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertNotNull($this->listener);
    }

    public function testCreateRemoteTemplateSkipsWhenSyncing(): void
    {
        // 创建一个处于同步状态的模板
        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setSyncing(true);

        // 由于设置了 syncing=true，监听器应该跳过远程调用
        $this->listener->createRemoteTemplate($template);

        // 验证模板没有被修改
        $this->assertNull($template->getTemplateId());
        $this->assertTrue($template->isSyncing());
    }

    public function testUpdateRemoteTemplateSkipsWhenSyncing(): void
    {
        // 创建一个处于同步状态的模板
        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setTemplateId('test-template-id');
        $template->setSyncing(true);

        // 由于设置了 syncing=true，监听器应该跳过远程调用
        $this->listener->updateRemoteTemplate($template);

        // 验证模板没有被修改
        $this->assertEquals('test-template-id', $template->getTemplateId());
        $this->assertTrue($template->isSyncing());
    }

    public function testDeleteRemoteTemplateSkipsWhenSyncing(): void
    {
        // 创建一个处于同步状态的模板
        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setTemplateId('test-template-id');
        $template->setSyncing(true);

        // 由于设置了 syncing=true，监听器应该跳过远程调用
        $this->listener->deleteRemoteTemplate($template);

        // 验证模板没有被修改
        $this->assertEquals('test-template-id', $template->getTemplateId());
        $this->assertTrue($template->isSyncing());
    }

    public function testCreateRemoteTemplateWithMissingRequiredFields(): void
    {
        // 创建一个缺少必需字段的模板
        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setSyncing(false); // 确保不是同步状态

        // 由于缺少必需字段，应该抛出异常
        $this->expectException(TemplateException::class);
        $this->expectExceptionMessage('模板账号不能为空');

        $this->listener->createRemoteTemplate($template);
    }

    public function testCreateRemoteTemplateWithMissingTemplateType(): void
    {
        // 创建一个账号，但缺少模板类型
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setAccount($account);
        $template->setSyncing(false); // 确保不是同步状态

        // 由于缺少模板类型，应该抛出异常
        $this->expectException(TemplateException::class);
        $this->expectExceptionMessage('模板类型不能为空');

        $this->listener->createRemoteTemplate($template);
    }

    public function testCreateRemoteTemplateWithValidData(): void
    {
        // 创建一个完整的测试账号
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        // 创建一个完整的模板
        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setTemplateContent('您的验证码是{code}，5分钟内有效。');
        $template->setAccount($account);
        $template->setTemplateType(TemplateType::NOTIFICATION);
        $template->setTemplateStatus(TemplateReviewStatus::REVIEWING);
        $template->setSyncing(false);

        // 由于使用了无效的腾讯云凭证，会抛出TemplateException
        $this->expectException(TemplateException::class);
        $this->expectExceptionMessage('创建模板失败：The SecretId is not found, please ensure that your SecretId is correct.');

        $this->listener->createRemoteTemplate($template);
    }

    public function testCreateRemoteTemplateValidationOrder(): void
    {
        // 创建一个只包含账号的模板
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setAccount($account);
        $template->setSyncing(false);

        // 应该首先检查模板类型
        $this->expectException(TemplateException::class);
        $this->expectExceptionMessage('模板类型不能为空');

        $this->listener->createRemoteTemplate($template);
    }

    public function testSyncingStatePreservation(): void
    {
        // 测试同步状态在各种操作后都得到保持
        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setSyncing(true);

        // 执行所有操作
        $this->listener->createRemoteTemplate($template);
        $this->listener->updateRemoteTemplate($template);
        $this->listener->deleteRemoteTemplate($template);

        // 验证同步状态保持不变
        $this->assertTrue($template->isSyncing());
        $this->assertNull($template->getTemplateId());
    }

    public function testUpdateRemoteTemplateWithMissingRequiredFields(): void
    {
        // 创建一个缺少必需字段的模板
        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setTemplateId('test-template-id');
        $template->setSyncing(false); // 确保不是同步状态

        // 由于缺少必需字段，应该抛出异常
        $this->expectException(TemplateException::class);
        $this->expectExceptionMessage('模板账号不能为空');

        $this->listener->updateRemoteTemplate($template);
    }

    public function testDeleteRemoteTemplateWithMissingRequiredFields(): void
    {
        // 创建一个缺少必需字段的模板
        $template = new SmsTemplate();
        $template->setTemplateName('Test Template');
        $template->setTemplateId('test-template-id');
        $template->setSyncing(false); // 确保不是同步状态

        // 由于缺少必需字段，应该抛出异常
        $this->expectException(TemplateException::class);
        $this->expectExceptionMessage('模板账号不能为空');

        $this->listener->deleteRemoteTemplate($template);
    }
}
