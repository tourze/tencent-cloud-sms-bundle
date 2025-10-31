<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20210111\SmsClient as TencentSmsClient;
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

    private MockObject|SdkService $mockSdkService;

    protected function onSetUp(): void
    {
        // 创建模拟的SdkService
        // 必须 mock SdkService 具体类的理由1: 该类没有定义接口，是具体的工具类实现
        // 理由2: 测试需要验证与具体方法的交互（getCredential, getHttpProfile, getClientProfile）
        // 理由3: 避免在测试中依赖真实的腾讯云 SDK 配置和网络请求
        $this->mockSdkService = $this->createMock(SdkService::class);

        // 直接创建SmsClient实例，注入mock的SdkService
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
        $this->smsClient = new SmsClient($this->mockSdkService);
    }

    public function testCreateWithValidAccount(): void
    {
        // 创建测试用的Account对象
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        // 创建模拟的返回对象
        $mockCredential = new Credential('test-secret-id', 'test-secret-key');
        $mockHttpProfile = new HttpProfile();
        $mockHttpProfile->setEndpoint('sms.tencentcloudapi.com');
        $mockClientProfile = new ClientProfile();
        $mockClientProfile->setHttpProfile($mockHttpProfile);

        // 配置模拟对象的行为
        $this->mockSdkService->expects($this->once())
            ->method('getCredential')
            ->with($account)
            ->willReturn($mockCredential)
        ;

        $this->mockSdkService->expects($this->once())
            ->method('getHttpProfile')
            ->with('sms.tencentcloudapi.com')
            ->willReturn($mockHttpProfile)
        ;

        $this->mockSdkService->expects($this->once())
            ->method('getClientProfile')
            ->with($mockHttpProfile)
            ->willReturn($mockClientProfile)
        ;

        // 调用create方法
        $result = $this->smsClient->create($account);

        // 验证返回值是否为TencentSmsClient实例
        $this->assertInstanceOf(TencentSmsClient::class, $result);
    }

    public function testCreateWithInvalidAccountThrowsException(): void
    {
        // 创建一个不完整的Account对象（缺少secretId和secretKey）
        $invalidAccount = new Account();
        $invalidAccount->setName('Invalid Account');
        // 故意不设置secretId和secretKey，触发异常

        // 配置模拟对象在调用getCredential时抛出异常
        $this->mockSdkService->expects($this->once())
            ->method('getCredential')
            ->with($invalidAccount)
            ->willThrowException(new \InvalidArgumentException('账号密钥信息不完整'))
        ;

        // 验证调用create方法时是否抛出了异常
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('账号密钥信息不完整');

        // 调用create方法，应该抛出异常
        $this->smsClient->create($invalidAccount);
    }

    public function testCreateWithEndpointError(): void
    {
        // 创建测试用的Account对象
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        // 创建模拟的Credential对象
        $mockCredential = new Credential('test-secret-id', 'test-secret-key');

        // 配置模拟对象的行为，getCredential正常返回
        $this->mockSdkService->expects($this->once())
            ->method('getCredential')
            ->with($account)
            ->willReturn($mockCredential)
        ;

        // 配置getHttpProfile在尝试设置无效endpoint时抛出异常
        $this->mockSdkService->expects($this->once())
            ->method('getHttpProfile')
            ->with('sms.tencentcloudapi.com')
            ->willThrowException(new \RuntimeException('Invalid endpoint'))
        ;

        // 验证调用create方法时是否抛出了异常
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid endpoint');

        // 调用create方法，应该抛出异常
        $this->smsClient->create($account);
    }
}
