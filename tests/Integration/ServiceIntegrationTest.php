<?php

namespace TencentCloudSmsBundle\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TencentCloudSmsBundle\Service\ImageService;
use TencentCloudSmsBundle\Service\PhoneNumberInfoService;
use TencentCloudSmsBundle\Service\SdkService;
use TencentCloudSmsBundle\Service\SmsClient;
use TencentCloudSmsBundle\Service\SmsSendService;
use TencentCloudSmsBundle\Service\SmsStatusService;
use TencentCloudSmsBundle\Service\StatisticsSyncService;
use TencentCloudSmsBundle\Service\StatusSyncService;

class ServiceIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'test',
            'debug' => true,
        ]);
    }

    public function testServiceRegistration(): void
    {
        $container = self::getContainer();
        
        // 验证核心服务是否被正确注册
        $this->assertTrue($container->has(SdkService::class));
        $this->assertTrue($container->has(SmsClient::class));
        $this->assertTrue($container->has(SmsSendService::class));
        $this->assertTrue($container->has(SmsStatusService::class));
        $this->assertTrue($container->has(StatusSyncService::class));
        $this->assertTrue($container->has(StatisticsSyncService::class));
        $this->assertTrue($container->has(PhoneNumberInfoService::class));
        $this->assertTrue($container->has(ImageService::class));
    }

    public function testSdkServiceDependencies(): void
    {
        $container = self::getContainer();
        $sdkService = $container->get(SdkService::class);
        
        // 验证SdkService实例是否正确创建
        $this->assertInstanceOf(SdkService::class, $sdkService);
    }

    public function testSmsClientDependencies(): void
    {
        $container = self::getContainer();
        $smsClient = $container->get(SmsClient::class);
        
        // 验证SmsClient实例是否正确创建及其依赖
        $this->assertInstanceOf(SmsClient::class, $smsClient);
        
        // 使用反射验证依赖是否被正确注入
        $reflection = new \ReflectionObject($smsClient);
        $sdkServiceProperty = $reflection->getProperty('sdkService');
        $sdkServiceProperty->setAccessible(true);
        
        $this->assertInstanceOf(SdkService::class, $sdkServiceProperty->getValue($smsClient));
    }

    public function testSmsSendServiceDependencies(): void
    {
        $container = self::getContainer();
        $smsSendService = $container->get(SmsSendService::class);
        
        // 验证SmsSendService实例是否正确创建及其依赖
        $this->assertInstanceOf(SmsSendService::class, $smsSendService);
        
        // 使用反射验证依赖是否被正确注入
        $reflection = new \ReflectionObject($smsSendService);
        
        $smsClientProperty = $reflection->getProperty('smsClient');
        $smsClientProperty->setAccessible(true);
        $this->assertInstanceOf(SmsClient::class, $smsClientProperty->getValue($smsSendService));
        
        $entityManagerProperty = $reflection->getProperty('entityManager');
        $entityManagerProperty->setAccessible(true);
        $this->assertNotNull($entityManagerProperty->getValue($smsSendService));
        
        $loggerProperty = $reflection->getProperty('logger');
        $loggerProperty->setAccessible(true);
        $this->assertNotNull($loggerProperty->getValue($smsSendService));
    }
    
    public function testServicesInteraction(): void
    {
        $container = self::getContainer();
        
        // 获取服务实例
        $sdkService = $container->get(SdkService::class);
        $smsClient = $container->get(SmsClient::class);
        
        // 验证服务之间的互操作性
        // 由于我们不想真正调用API，这里只验证服务间的基本交互能力
        $httpProfile = $sdkService->getHttpProfile();
        $clientProfile = $sdkService->getClientProfile($httpProfile);
        
        // 验证得到的对象是否符合预期类型
        $this->assertInstanceOf('TencentCloud\Common\Profile\HttpProfile', $httpProfile);
        $this->assertInstanceOf('TencentCloud\Common\Profile\ClientProfile', $clientProfile);
    }

    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }
} 