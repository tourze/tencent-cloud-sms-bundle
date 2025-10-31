<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncUnknownStatusCommand;
use TencentCloudSmsBundle\Service\SmsStatusService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncUnknownStatusCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncUnknownStatusCommandTest extends AbstractCommandTestCase
{
    private SmsStatusService&MockObject $smsStatusService;

    protected function onSetUp(): void
    {
        // 创建模拟服务
        // 必须使用具体类 SmsStatusService 而不是接口的原因：
        // 1. SmsStatusService 是具体服务类，没有定义接口，在依赖注入容器中直接作为服务使用
        // 2. 测试需要验证与该服务的特定方法调用，包括 syncUnknownStatus() 方法的参数传递
        // 3. 该服务封装了复杂的腾讯云 SDK 未知状态同步逻辑，需要测试具体的方法签名和行为
        $this->smsStatusService = $this->createMock(SmsStatusService::class);

        // 注册模拟服务到容器
        $container = self::getContainer();
        $container->set(SmsStatusService::class, $this->smsStatusService);
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncUnknownStatusCommand::class);
        $this->assertInstanceOf(SyncUnknownStatusCommand::class, $command);

        return new CommandTester($command);
    }

    public function testCommandName(): void
    {
        $command = self::getService(SyncUnknownStatusCommand::class);
        $this->assertEquals(SyncUnknownStatusCommand::NAME, $command->getName());
        $this->assertEquals('tencent-cloud:sms:sync:unknown-status', $command->getName());
    }

    public function testCommandDescription(): void
    {
        $command = self::getService(SyncUnknownStatusCommand::class);
        $this->assertEquals('同步未知状态的短信记录', $command->getDescription());
    }

    public function testExecuteWithDefaultLimit(): void
    {
        // 配置服务调用
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncUnknownStatus')
            ->with(100) // 默认limit值
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('未知状态短信同步完成', $output);
    }

    public function testExecuteWithCustomLimit(): void
    {
        // 配置服务调用
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncUnknownStatus')
            ->with(50) // 自定义limit值
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute(['--limit' => '50']);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('未知状态短信同步完成', $output);
    }

    public function testOptionLimit(): void
    {
        $command = self::getService(SyncUnknownStatusCommand::class);
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('limit'));
        $option = $definition->getOption('limit');
        $this->assertTrue($option->isValueOptional());
        $this->assertEquals('每次同步的最大记录数', $option->getDescription());
        $this->assertEquals(100, $option->getDefault());
    }

    public function testExecuteWithException(): void
    {
        // 配置服务抛出异常
        $this->smsStatusService
            ->expects($this->once())
            ->method('syncUnknownStatus')
            ->with(100)
            ->willThrowException(new \Exception('同步失败'))
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::FAILURE, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('未知状态短信同步失败: 同步失败', $output);
    }
}
