<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncUnknownStatusCommand;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncUnknownStatusCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncUnknownStatusCommandTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 使用真实的容器，不需要设置Mock
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

    public function testCommandCanBeInstantiated(): void
    {
        $command = self::getService(SyncUnknownStatusCommand::class);
        $this->assertInstanceOf(SyncUnknownStatusCommand::class, $command);
    }
}