<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncSmsStatusCommand;
use TencentCloudSmsBundle\Service\SmsStatusService;

class SyncSmsStatusCommandTest extends TestCase
{
    private SyncSmsStatusCommand $command;
    private SmsStatusService&MockObject $smsStatusService;
    private CommandTester $commandTester;

    public function testCommandName(): void
    {
        $this->assertEquals('tencent-cloud:sms:sync:status', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertEquals('同步短信发送状态', $this->command->getDescription());
    }

    public function testExecuteSuccess(): void
    {
        // 配置服务调用
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncStatus');

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('短信状态同步完成', $output);
    }

    public function testExecuteWithException(): void
    {
        // 配置服务抛出异常
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncStatus')
            ->willThrowException(new \Exception('同步失败'));

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::FAILURE, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('短信状态同步失败: 同步失败', $output);
    }

    protected function setUp(): void
    {
        // 创建模拟服务
        $this->smsStatusService = $this->createMock(SmsStatusService::class);

        // 创建命令实例
        $this->command = new SyncSmsStatusCommand(
            $this->smsStatusService
        );

        // 设置命令测试器
        $application = new Application();
        $application->add($this->command);
        $this->commandTester = new CommandTester($this->command);
    }
}
