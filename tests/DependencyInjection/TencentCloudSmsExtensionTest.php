<?php

namespace TencentCloudSmsBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TencentCloudSmsBundle\DependencyInjection\TencentCloudSmsExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(TencentCloudSmsExtension::class)]
final class TencentCloudSmsExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private TencentCloudSmsExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new TencentCloudSmsExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    public function testGetConfigDir(): void
    {
        $reflection = new \ReflectionClass($this->extension);
        $method = $reflection->getMethod('getConfigDir');
        $method->setAccessible(true);
        $configDir = $method->invoke($this->extension);

        if (!is_string($configDir)) {
            self::fail('Config dir must be string');
        }

        $this->assertStringContainsString('Resources/config', $configDir);
        $this->assertDirectoryExists($configDir);
    }

    public function testLoad(): void
    {
        $this->extension->load([], $this->container);

        $this->assertNotEmpty($this->container->getDefinitions());
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\Service\SdkService'));
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\Service\SmsClient'));
        $this->assertTrue($this->container->hasDefinition('TencentCloudSmsBundle\Service\SmsSendService'));
    }
}
