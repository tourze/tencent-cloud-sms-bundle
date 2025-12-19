<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Service\SdkService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SdkService::class)]
#[RunTestsInSeparateProcesses]
final class SdkServiceTest extends AbstractIntegrationTestCase
{
    private SdkService $sdkService;

    private Account $mockAccount;

    protected function onSetUp(): void
    {
        // 从容器获取SdkService实例进行单元测试
        $this->sdkService = self::getService(SdkService::class);

        // 创建模拟的Account对象
        $this->mockAccount = new Account();
        $this->mockAccount->setName('测试账号');
        $this->mockAccount->setSecretId('test-secret-id');
        $this->mockAccount->setSecretKey('test-secret-key');
    }

    public function testGetCredentialWithValidAccount(): void
    {
        // 使用模拟账号生成凭证
        $credential = $this->sdkService->getCredential($this->mockAccount);

        // 验证返回的是否为Credential对象
        $this->assertNotNull($credential);

        // 获取Credential的私有属性进行验证（通过反射）
        $reflection = new \ReflectionObject($credential);

        $secretIdProperty = $reflection->getProperty('secretId');
        $secretIdProperty->setAccessible(true);
        $this->assertEquals('test-secret-id', $secretIdProperty->getValue($credential));

        $secretKeyProperty = $reflection->getProperty('secretKey');
        $secretKeyProperty->setAccessible(true);
        $this->assertEquals('test-secret-key', $secretKeyProperty->getValue($credential));
    }

    public function testGetHttpProfileWithoutEndpoint(): void
    {
        // 不提供endpoint参数
        $httpProfile = $this->sdkService->getHttpProfile();

        // 验证返回的是否为HttpProfile对象
        $this->assertNotNull($httpProfile);

        // 验证默认endpoint是否未设置（保持默认值）
        $reflection = new \ReflectionObject($httpProfile);
        $endpointProperty = $reflection->getProperty('endpoint');
        $endpointProperty->setAccessible(true);

        // HttpProfile的默认endpoint是空字符串
        $this->assertEquals('', $endpointProperty->getValue($httpProfile));
    }

    public function testGetHttpProfileWithEndpoint(): void
    {
        // 提供自定义endpoint
        $customEndpoint = 'custom.endpoint.com';
        $httpProfile = $this->sdkService->getHttpProfile($customEndpoint);

        // 验证返回的是否为HttpProfile对象
        $this->assertNotNull($httpProfile);

        // 验证endpoint是否被正确设置
        $reflection = new \ReflectionObject($httpProfile);
        $endpointProperty = $reflection->getProperty('endpoint');
        $endpointProperty->setAccessible(true);
        $this->assertEquals($customEndpoint, $endpointProperty->getValue($httpProfile));
    }

    public function testGetClientProfileWithoutHttpProfile(): void
    {
        // 不提供HttpProfile参数
        $clientProfile = $this->sdkService->getClientProfile();

        // 验证返回的是否为ClientProfile对象
        $this->assertNotNull($clientProfile);

        // 验证是否使用了默认HttpProfile
        $reflection = new \ReflectionObject($clientProfile);
        $httpProfileProperty = $reflection->getProperty('httpProfile');
        $httpProfileProperty->setAccessible(true);
        $this->assertNotNull($httpProfileProperty->getValue($clientProfile));
    }

    public function testGetClientProfileWithHttpProfile(): void
    {
        // 创建自定义的HttpProfile
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint('test.endpoint.com');

        // 使用自定义HttpProfile创建ClientProfile
        $clientProfile = $this->sdkService->getClientProfile($httpProfile);

        // 验证返回的是否为ClientProfile对象
        $this->assertNotNull($clientProfile);

        // 验证是否使用了提供的HttpProfile
        $reflection = new \ReflectionObject($clientProfile);
        $httpProfileProperty = $reflection->getProperty('httpProfile');
        $httpProfileProperty->setAccessible(true);
        $this->assertSame($httpProfile, $httpProfileProperty->getValue($clientProfile));
    }
}
