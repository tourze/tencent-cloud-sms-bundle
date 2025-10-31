<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncSmsStatusCommand;
use TencentCloudSmsBundle\Service\SmsStatusService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncSmsStatusCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncSmsStatusCommandTest extends AbstractCommandTestCase
{
    private SmsStatusService&MockObject $smsStatusService;

    public function testCommandName(): void
    {
        $command = self::getService(SyncSmsStatusCommand::class);
        $this->assertEquals('tencent-cloud:sms:sync:status', $command->getName());
    }

    public function testCommandDescription(): void
    {
        $command = self::getService(SyncSmsStatusCommand::class);
        $this->assertEquals('同步短信发送状态', $command->getDescription());
    }

    public function testExecuteSuccess(): void
    {
        // 配置服务调用
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncStatus')
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('短信状态同步完成', $output);
    }

    public function testExecuteWithException(): void
    {
        // 配置服务抛出异常
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncStatus')
            ->willThrowException(new \Exception('同步失败'))
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::FAILURE, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('短信状态同步失败: 同步失败', $output);
    }

    protected function onSetUp(): void
    {
        // 创建模拟服务
        // 必须使用具体类 SmsStatusService 而不是接口的原因：
        // 1. SmsStatusService 是具体服务类，没有定义接口，在依赖注入容器中直接作为服务使用
        // 2. 测试需要验证与该服务的特定方法调用，包括 syncStatus() 方法的行为
        // 3. 该服务封装了复杂的腾讯云 SDK 状态同步逻辑，需要测试具体的方法签名和行为
        $this->smsStatusService = $this->createMock(SmsStatusService::class);

        // 注册模拟服务到容器
        $container = self::getContainer();
        $container->set(SmsStatusService::class, $this->smsStatusService);
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncSmsStatusCommand::class);
        $this->assertInstanceOf(SyncSmsStatusCommand::class, $command);

        return new CommandTester($command);
    }
}
