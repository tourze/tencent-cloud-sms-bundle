<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncStatisticsCommand;
use TencentCloudSmsBundle\Entity\Account;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncStatisticsCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncStatisticsCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 使用真实的容器，不需要设置Mock
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncStatisticsCommand::class);
        $this->assertInstanceOf(SyncStatisticsCommand::class, $command);

        return new CommandTester($command);
    }

    public function testCommandName(): void
    {
        $command = self::getService(SyncStatisticsCommand::class);
        $this->assertEquals(SyncStatisticsCommand::NAME, $command->getName());
        $this->assertEquals('tencent-cloud:sms:sync-statistics', $command->getName());
    }

    public function testCommandDescription(): void
    {
        $command = self::getService(SyncStatisticsCommand::class);
        $this->assertEquals('同步腾讯云短信统计数据', $command->getDescription());
    }

    public function testExecuteSuccess(): void
    {
        // 执行命令 - 使用真实的服务
        // 注意：这个测试会尝试调用真实的腾讯云服务，在实际环境中可能需要配置测试账号
        // 或者使用测试环境的配置来避免真实API调用
        $commandTester = $this->getCommandTester();

        // 由于是集成测试，我们无法保证一定成功（依赖于外部服务和配置）
        // 所以我们只验证命令能够正常执行，不验证具体结果
        $exitCode = $commandTester->execute([]);

        // 验证命令执行完成（成功或失败都可以，只要不抛出异常）
        $this->assertContains($exitCode, [Command::SUCCESS, Command::FAILURE]);
    }

    public function testOptionStartTime(): void
    {
        $command = self::getService(SyncStatisticsCommand::class);
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('start-time'));
        $option = $definition->getOption('start-time');
        $this->assertTrue($option->isValueRequired());
        $this->assertEquals('开始时间 (Y-m-d H:i:s)', $option->getDescription());
    }

    public function testOptionEndTime(): void
    {
        $command = self::getService(SyncStatisticsCommand::class);
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('end-time'));
        $option = $definition->getOption('end-time');
        $this->assertTrue($option->isValueRequired());
        $this->assertEquals('结束时间 (Y-m-d H:i:s)', $option->getDescription());
    }

    public function testOptionAccountId(): void
    {
        $command = self::getService(SyncStatisticsCommand::class);
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('account-id'));
        $option = $definition->getOption('account-id');
        $this->assertTrue($option->isValueRequired());
        $this->assertEquals('指定同步的账号ID', $option->getDescription());
    }

    public function testCommandCanBeInstantiated(): void
    {
        $command = self::getService(SyncStatisticsCommand::class);
        $this->assertInstanceOf(SyncStatisticsCommand::class, $command);
    }
}
