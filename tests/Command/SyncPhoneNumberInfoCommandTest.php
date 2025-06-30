<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncPhoneNumberInfoCommand;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Repository\AccountRepository;
use TencentCloudSmsBundle\Service\PhoneNumberInfoService;

class SyncPhoneNumberInfoCommandTest extends TestCase
{
    private SyncPhoneNumberInfoCommand $command;
    private PhoneNumberInfoService&MockObject $phoneNumberInfoService;
    private AccountRepository&MockObject $accountRepository;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // 创建模拟服务
        $this->phoneNumberInfoService = $this->createMock(PhoneNumberInfoService::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);

        // 创建命令实例
        $this->command = new SyncPhoneNumberInfoCommand(
            $this->phoneNumberInfoService,
            $this->accountRepository
        );

        // 设置命令测试器
        $application = new Application();
        $application->add($this->command);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testCommandName(): void
    {
        $this->assertEquals(SyncPhoneNumberInfoCommand::NAME, $this->command->getName());
        $this->assertEquals('tencent-cloud:sms:sync:phone-number-info', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertEquals('同步手机号码信息', $this->command->getDescription());
    }

    public function testExecuteWithNoAccounts(): void
    {
        // 配置仓储返回空数组
        $this->accountRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    public function testExecuteWithAccounts(): void
    {
        // 创建模拟账号
        $account1 = $this->createMock(Account::class);
        $account1->method('getId')->willReturn(1);

        $account2 = $this->createMock(Account::class);
        $account2->method('getId')->willReturn(2);

        $accounts = [$account1, $account2];

        // 配置仓储返回账号列表
        $this->accountRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($accounts);

        // 配置服务调用
        $this->phoneNumberInfoService
            ->expects($this->exactly(2))
            ->method('syncPhoneNumberInfo')
            ->with($this->logicalOr($account1, $account2));

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('账号 1 手机号码信息同步完成', $output);
        $this->assertStringContainsString('账号 2 手机号码信息同步完成', $output);
    }

    public function testExecuteWithException(): void
    {
        // 创建模拟账号
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn(1);

        // 配置仓储返回账号
        $this->accountRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$account]);

        // 配置服务抛出异常
        $this->phoneNumberInfoService
            ->expects($this->once())
            ->method('syncPhoneNumberInfo')
            ->with($account)
            ->willThrowException(new \Exception('同步失败'));

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('账号 1 手机号码信息同步失败: 同步失败', $output);
    }
}
