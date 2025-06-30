<?php

namespace TencentCloudSmsBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncStatisticsCommand;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Repository\AccountRepository;
use TencentCloudSmsBundle\Service\StatisticsSyncService;

class SyncStatisticsCommandTest extends TestCase
{
    private SyncStatisticsCommand $command;
    private StatisticsSyncService&MockObject $syncService;
    private AccountRepository&MockObject $accountRepository;
    private EntityManagerInterface&MockObject $entityManager;
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        // 创建模拟服务
        $this->syncService = $this->createMock(StatisticsSyncService::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // 创建命令实例
        $this->command = new SyncStatisticsCommand(
            $this->syncService,
            $this->accountRepository,
            $this->entityManager
        );

        // 设置命令测试器
        $application = new Application();
        $application->add($this->command);
        $this->commandTester = new CommandTester($this->command);
    }

    public function testCommandName(): void
    {
        $this->assertEquals(SyncStatisticsCommand::NAME, $this->command->getName());
        $this->assertEquals('tencent-cloud:sms:sync-statistics', $this->command->getName());
    }

    public function testCommandDescription(): void
    {
        $this->assertEquals('同步腾讯云短信统计数据', $this->command->getDescription());
    }

    public function testExecuteWithDefaultOptions(): void
    {
        // 创建模拟账号
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn(1);

        // 配置仓储返回账号列表
        $this->accountRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['isEnabled' => true])
            ->willReturn([$account]);

        // 配置服务调用
        $this->syncService
            ->expects($this->once())
            ->method('sync')
            ->with(
                $this->isInstanceOf(\DateTimeImmutable::class),
                $this->isInstanceOf(\DateTimeImmutable::class),
                $account
            );

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Successfully synced statistics for account: 1', $output);
    }

    public function testExecuteWithSpecificAccountId(): void
    {
        // 创建模拟账号引用
        $accountReference = $this->createMock(Account::class);
        $accountReference->method('getId')->willReturn(123);

        // 配置EntityManager返回账号引用
        $this->entityManager
            ->expects($this->once())
            ->method('getReference')
            ->with(Account::class, 123)
            ->willReturn($accountReference);

        // 配置服务调用
        $this->syncService
            ->expects($this->once())
            ->method('sync')
            ->with(
                $this->isInstanceOf(\DateTimeImmutable::class),
                $this->isInstanceOf(\DateTimeImmutable::class),
                $accountReference
            );

        // 执行命令
        $exitCode = $this->commandTester->execute(['--account-id' => '123']);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Successfully synced statistics for account: 123', $output);
    }

    public function testExecuteWithCustomTimeRange(): void
    {
        // 创建模拟账号
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn(1);

        // 配置仓储返回账号列表
        $this->accountRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['isEnabled' => true])
            ->willReturn([$account]);

        // 配置服务调用
        $this->syncService
            ->expects($this->once())
            ->method('sync')
            ->with(
                $this->isInstanceOf(\DateTimeImmutable::class),
                $this->isInstanceOf(\DateTimeImmutable::class),
                $account
            );

        // 执行命令
        $exitCode = $this->commandTester->execute([
            '--start-time' => '2023-01-01 00:00:00',
            '--end-time' => '2023-01-02 00:00:00'
        ]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    public function testExecuteWithException(): void
    {
        // 创建模拟账号
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn(1);

        // 配置仓储返回账号
        $this->accountRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['isEnabled' => true])
            ->willReturn([$account]);

        // 配置服务抛出异常
        $this->syncService
            ->expects($this->once())
            ->method('sync')
            ->willThrowException(new \Exception('同步失败'));

        // 执行命令
        $exitCode = $this->commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::FAILURE, $exitCode);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Failed to sync statistics for account 1: 同步失败', $output);
    }
}
