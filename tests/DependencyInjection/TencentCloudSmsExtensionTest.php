<?php

namespace TencentCloudSmsBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TencentCloudSmsBundle\DependencyInjection\TencentCloudSmsExtension;

class TencentCloudSmsExtensionTest extends TestCase
{
    private TencentCloudSmsExtension $extension;
    private ContainerBuilder $container;

    public function testLoad(): void
    {
        // 调用load方法
        $this->extension->load([], $this->container);

        // 验证服务是否被正确加载
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\\Service\\SdkService'));
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\\Service\\SmsClient'));
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\\Service\\SmsSendService'));
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\\Service\\SmsStatusService'));
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\\Service\\StatusSyncService'));
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\\Service\\StatisticsSyncService'));
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\\Service\\PhoneNumberInfoService'));
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\\Service\\ImageService'));
    }

    public function testLoadWithConfigs(): void
    {
        // 测试传入配置数组
        $configs = [
            ['some_config' => 'value']
        ];

        $this->extension->load($configs, $this->container);

        // 验证服务仍然被正确加载
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\\Service\\SdkService'));
    }

    public function testGetAlias(): void
    {
        // 测试别名获取
        $this->assertEquals('tencent_cloud_sms', $this->extension->getAlias());
    }

    protected function setUp(): void
    {
        $this->extension = new TencentCloudSmsExtension();
        $this->container = new ContainerBuilder();
    }
}
