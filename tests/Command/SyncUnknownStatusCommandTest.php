<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncUnknownStatusCommand;
use TencentCloudSmsBundle\Service\SmsStatusService;

class SyncUnknownStatusCommandTest extends TestCase
{
    private SyncUnknownStatusCommand $command;
    private SmsStatusService&MockObject $smsStatusService;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // 创建模拟服务
        $this->smsStatusService = $this->createMock(SmsStatusService::class);

        // 创建命令实例
        $this->command = new SyncUnknownStatusCommand(
            $this->smsStatusService
        );

        // 设置命令测试器
        $application = new Application();
        $application->add($this->command);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testCommandName(): void
    {
        $this->assertEquals(SyncUnknownStatusCommand::NAME, $this->command->getName());
        $this->assertEquals('tencent-cloud:sms:sync:unknown-status', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertEquals('同步未知状态的短信记录', $this->command->getDescription());
    }

    public function testExecuteWithDefaultLimit(): void
    {
        // 配置服务调用
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncUnknownStatus')
            ->with(100); // 默认limit值

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('未知状态短信同步完成', $output);
    }

    public function testExecuteWithCustomLimit(): void
    {
        // 配置服务调用
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncUnknownStatus')
            ->with(50); // 自定义limit值

        // 执行命令
        $exitCode = $this->commandTester->execute(['--limit' => '50']);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('未知状态短信同步完成', $output);
    }

    public function testExecuteWithException(): void
    {
        // 配置服务抛出异常
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncUnknownStatus')
            ->with(100)
            ->willThrowException(new \Exception('同步失败'));

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::FAILURE, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('未知状态短信同步失败: 同步失败', $output);
    }
}
