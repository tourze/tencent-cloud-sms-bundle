<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncTemplateStatusCommand;
use TencentCloudSmsBundle\Service\StatusSyncService;

class SyncTemplateStatusCommandTest extends TestCase
{
    private SyncTemplateStatusCommand $command;
    private StatusSyncService&MockObject $statusSyncService;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // 创建模拟服务
        $this->statusSyncService = $this->createMock(StatusSyncService::class);

        // 创建命令实例
        $this->command = new SyncTemplateStatusCommand(
            $this->statusSyncService
        );

        // 设置命令测试器
        $application = new Application();
        $application->add($this->command);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testCommandName(): void
    {
        $this->assertEquals(SyncTemplateStatusCommand::NAME, $this->command->getName());
        $this->assertEquals('tencent-cloud:sms:sync:template-status', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertEquals('同步短信模板状态', $this->command->getDescription());
    }

    public function testExecuteSuccess(): void
    {
        // 配置服务调用
        $this->statusSyncService
            ->expects($this->once())
            ->method('syncTemplates');

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('短信模板状态同步完成', $output);
    }

    public function testExecuteWithException(): void
    {
        // 配置服务抛出异常
        $this->statusSyncService
            ->expects($this->once())
            ->method('syncTemplates')
            ->willThrowException(new \Exception('同步失败'));

        // 执行命令，期望异常被抛出
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('同步失败');

        $this->commandTester->execute([]);
    }
}
