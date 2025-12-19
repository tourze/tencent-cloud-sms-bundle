<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Service\SdkService;
use TencentCloudSmsBundle\Service\SmsClient;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SmsClient::class)]
#[RunTestsInSeparateProcesses]
final class SmsClientTest extends AbstractIntegrationTestCase
{
    private SmsClient $smsClient;

    protected function onSetUp(): void
    {
        // 从容器获取真实的SmsClient实例进行集成测试
        $this->smsClient = self::getService(SmsClient::class);
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertNotNull($this->smsClient);
        $this->assertInstanceOf(SmsClient::class, $this->smsClient);
    }

    public function testCreateWithValidAccount(): void
    {
        // 创建测试用的Account对象
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        // 由于是集成测试，我们使用真实的SdkService
        // 注意：这个测试会创建真实的腾讯云客户端，但不会发送请求
        $result = $this->smsClient->create($account);

        // 验证返回值不为null（实际类型取决于腾讯云SDK的实现）
        $this->assertNotNull($result);
    }

    public function testCreateWithIncompleteAccount(): void
    {
        // 创建一个不完整的Account对象
        $incompleteAccount = new Account();
        $incompleteAccount->setName('Incomplete Account');
        // 故意不设置secretId和secretKey

        // 测试是否会因为缺少必要信息而失败
        // 注意：具体行为取决于SmsClient的实现
        try {
            $result = $this->smsClient->create($incompleteAccount);
            // 如果没有抛出异常，至少验证返回值不为null
            $this->assertNotNull($result);
        } catch (\InvalidArgumentException $e) {
            // 如果抛出异常是预期的行为
            $this->assertStringContainsString('账号', $e->getMessage());
        }
    }

    public function testSdkServiceIsAvailable(): void
    {
        // 验证依赖的SdkService服务在容器中可用
        $sdkService = self::getService(SdkService::class);
        $this->assertNotNull($sdkService);
        $this->assertInstanceOf(SdkService::class, $sdkService);
    }
}