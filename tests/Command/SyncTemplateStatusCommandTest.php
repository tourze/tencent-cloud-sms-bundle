<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncTemplateStatusCommand;
use TencentCloudSmsBundle\Service\StatusSyncService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncTemplateStatusCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncTemplateStatusCommandTest extends AbstractCommandTestCase
{
    private StatusSyncService&MockObject $statusSyncService;

    protected function onSetUp(): void
    {
        // 创建模拟服务
        // 必须使用具体类 StatusSyncService 而不是接口的原因：
        // 1. StatusSyncService 没有对应的接口，它是一个具体的服务类，直接依赖注入使用
        // 2. 测试需要验证与该服务的具体交互行为，包括 syncTemplates() 方法的调用
        // 3. 该服务封装了复杂的腾讯云 SDK 调用逻辑，使用具体类可以确保测试覆盖真实的方法签名
        $this->statusSyncService = $this->createMock(StatusSyncService::class);

        // 注册模拟服务到容器
        $container = self::getContainer();
        $container->set(StatusSyncService::class, $this->statusSyncService);
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncTemplateStatusCommand::class);
        $this->assertInstanceOf(SyncTemplateStatusCommand::class, $command);

        return new CommandTester($command);
    }

    public function testCommandName(): void
    {
        $command = self::getService(SyncTemplateStatusCommand::class);
        $this->assertEquals(SyncTemplateStatusCommand::NAME, $command->getName());
        $this->assertEquals('tencent-cloud:sms:sync:template-status', $command->getName());
    }

    public function testCommandDescription(): void
    {
        $command = self::getService(SyncTemplateStatusCommand::class);
        $this->assertEquals('同步短信模板状态', $command->getDescription());
    }

    public function testExecuteSuccess(): void
    {
        // 配置服务调用
        $this->statusSyncService
            ->expects($this->once())
            ->method('syncTemplates')
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('短信模板状态同步完成', $output);
    }

    public function testExecuteWithException(): void
    {
        // 配置服务抛出异常
        $this->statusSyncService
            ->expects($this->once())
            ->method('syncTemplates')
            ->willThrowException(new \Exception('同步失败'))
        ;

        // 执行命令，期望异常被抛出
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('同步失败');

        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);
    }
}
