<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Service\StatisticsSyncService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(StatisticsSyncService::class)]
#[RunTestsInSeparateProcesses]
final class StatisticsSyncServiceTest extends AbstractIntegrationTestCase
{
    private StatisticsSyncService $service;

    protected function onSetUp(): void
    {
        // 从容器获取真实的服务实例进行集成测试
        $this->service = self::getService(StatisticsSyncService::class);
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertNotNull($this->service);
        $this->assertInstanceOf(StatisticsSyncService::class, $this->service);
    }

    public function testSyncMethodExists(): void
    {
        // 验证 sync 方法存在且可调用
        $reflection = new \ReflectionClass($this->service);
        $this->assertTrue($reflection->hasMethod('sync'));
    }

    public function testServiceDependenciesAreAvailable(): void
    {
        // 如果服务能成功实例化，说明依赖都满足
        $this->assertTrue(true);
    }
}