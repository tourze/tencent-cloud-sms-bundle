<?php

namespace TencentCloudSmsBundle\Tests\Unit\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20210111\SmsClient as TencentSmsClient;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Service\SdkService;
use TencentCloudSmsBundle\Service\SmsClient;

class SmsClientTest extends TestCase
{
    private SmsClient $smsClient;
    private MockObject|SdkService $mockSdkService;
    private Account $mockAccount;
    private Credential $mockCredential;
    private HttpProfile $mockHttpProfile;
    private ClientProfile $mockClientProfile;

    protected function setUp(): void
    {
        // 创建模拟的SdkService
        $this->mockSdkService = $this->createMock(SdkService::class);
        
        // 创建SmsClient实例，注入模拟的SdkService
        $this->smsClient = new SmsClient($this->mockSdkService);
        
        // 创建模拟的Account对象
        $this->mockAccount = new Account();
        $this->mockAccount->setName('测试账号');
        $this->mockAccount->setSecretId('test-secret-id');
        $this->mockAccount->setSecretKey('test-secret-key');
        
        // 创建模拟的Credential、HttpProfile和ClientProfile对象
        $this->mockCredential = new Credential('test-secret-id', 'test-secret-key');
        $this->mockHttpProfile = new HttpProfile();
        $this->mockHttpProfile->setEndpoint('sms.tencentcloudapi.com');
        $this->mockClientProfile = new ClientProfile();
        $this->mockClientProfile->setHttpProfile($this->mockHttpProfile);
    }

    public function testCreate_withValidAccount(): void
    {
        // 配置模拟对象的行为
        $this->mockSdkService->expects($this->once())
            ->method('getCredential')
            ->with($this->mockAccount)
            ->willReturn($this->mockCredential);
            
        $this->mockSdkService->expects($this->once())
            ->method('getHttpProfile')
            ->with('sms.tencentcloudapi.com')
            ->willReturn($this->mockHttpProfile);
            
        $this->mockSdkService->expects($this->once())
            ->method('getClientProfile')
            ->with($this->mockHttpProfile)
            ->willReturn($this->mockClientProfile);
        
        // 调用create方法
        $result = $this->smsClient->create($this->mockAccount);
        
        // 验证返回值是否为TencentSmsClient实例
        $this->assertInstanceOf(TencentSmsClient::class, $result);
        
        // 验证TencentSmsClient实例是否使用了正确的凭证和配置
        // 注意：由于TencentSmsClient的内部属性是私有的，我们无法直接访问
        // 这里我们只能验证实例类型，实际项目中可以通过集成测试进一步验证功能
    }

    public function testCreate_withInvalidAccountThrowsException(): void
    {
        // 创建一个不完整的Account对象（缺少secretId和secretKey）
        $invalidAccount = new Account();
        $invalidAccount->setName('Invalid Account');
        
        // 配置模拟对象在调用getCredential时抛出异常
        $this->mockSdkService->expects($this->once())
            ->method('getCredential')
            ->with($invalidAccount)
            ->willThrowException(new \InvalidArgumentException('Invalid credentials'));
            
        // 验证调用create方法时是否抛出了异常
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid credentials');
        
        // 调用create方法，应该抛出异常
        $this->smsClient->create($invalidAccount);
    }

    public function testCreate_withEndpointError(): void
    {
        // 配置模拟对象的行为，getCredential正常返回
        $this->mockSdkService->expects($this->once())
            ->method('getCredential')
            ->with($this->mockAccount)
            ->willReturn($this->mockCredential);
            
        // 配置getHttpProfile在尝试设置无效endpoint时抛出异常
        $this->mockSdkService->expects($this->once())
            ->method('getHttpProfile')
            ->with('sms.tencentcloudapi.com')
            ->willThrowException(new \RuntimeException('Invalid endpoint'));
            
        // 验证调用create方法时是否抛出了异常
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid endpoint');
        
        // 调用create方法，应该抛出异常
        $this->smsClient->create($this->mockAccount);
    }
} 