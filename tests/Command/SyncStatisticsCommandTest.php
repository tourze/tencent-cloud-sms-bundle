<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncStatisticsCommand;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Repository\AccountRepository;
use TencentCloudSmsBundle\Service\StatisticsSyncService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncStatisticsCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncStatisticsCommandTest extends AbstractCommandTestCase
{
    private StatisticsSyncService&MockObject $syncService;

    private AccountRepository&MockObject $accountRepository;

    protected function onSetUp(): void
    {
        // 创建模拟服务
        // 必须使用具体类 StatisticsSyncService 而不是接口的原因：
        // 1. StatisticsSyncService 没有对应的接口，它是一个具体的业务服务类
        // 2. 测试需要验证与该服务的同步方法调用，包括 sync() 方法的复杂参数传递
        // 3. 该服务封装了多种统计数据同步逻辑，需要测试具体的方法签名
        $this->syncService = $this->createMock(StatisticsSyncService::class);

        // 必须使用具体类 AccountRepository 而不是接口的原因：
        // 1. AccountRepository 继承自 ServiceEntityRepository，没有单独的接口定义
        // 2. 测试需要验证仓储的查询方法调用，包括 findBy() 方法的特定参数
        // 3. 该仓储类提供了特定的账号查询功能，需要测试具体的查询行为
        $this->accountRepository = $this->createMock(AccountRepository::class);

        // 注册模拟服务到容器
        $container = self::getContainer();
        $container->set(StatisticsSyncService::class, $this->syncService);
        $container->set(AccountRepository::class, $this->accountRepository);
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

    public function testExecuteWithDefaultOptions(): void
    {
        // 创建模拟账号
        // 必须使用具体类 Account 而不是接口的原因：
        // 1. Account 是具体的实体类，它是 Doctrine ORM 映射的数据库实体，没有抽象接口
        // 2. 测试需要验证与账号实体的特定属性访问，包括 getId() 方法的返回值
        // 3. 该实体包含复杂的属性和关联关系，使用具体类可以确保测试覆盖真实的实体结构
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn(1);

        // 配置仓储返回账号列表
        $this->accountRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['isEnabled' => true])
            ->willReturn([$account])
        ;

        // 配置服务调用
        $this->syncService
            ->expects($this->once())
            ->method('sync')
            ->with(
                self::isInstanceOf(\DateTimeImmutable::class),
                self::isInstanceOf(\DateTimeImmutable::class),
                $account
            )
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully synced statistics for account: 1', $output);
    }

    public function testExecuteWithSpecificAccountId(): void
    {
        // 创建模拟账号引用
        // 必须使用具体类 Account 而不是接口的原因：
        // 1. Account 是具体的实体类，它是 Doctrine ORM 映射的数据库实体，没有抽象接口
        // 2. 测试需要模拟 EntityManager 的 getReference() 方法返回的实体代理对象
        // 3. 该实体包含复杂的属性和关联关系，使用具体类可以确保测试覆盖真实的实体结构
        $accountReference = self::getEntityManager()->getReference(Account::class, 123);

        // 配置服务调用
        $this->syncService
            ->expects($this->once())
            ->method('sync')
            ->with(
                self::isInstanceOf(\DateTimeImmutable::class),
                self::isInstanceOf(\DateTimeImmutable::class),
                $accountReference
            )
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute(['--account-id' => '123']);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully synced statistics for account: 123', $output);
    }

    public function testExecuteWithCustomTimeRange(): void
    {
        // 创建模拟账号
        // 必须使用具体类 Account 而不是接口的原因：
        // 1. Account 是具体的实体类，它是 Doctrine ORM 映射的数据库实体，没有抽象接口
        // 2. 测试需要验证与账号实体的特定属性访问，包括 getId() 方法的返回值
        // 3. 该实体包含复杂的属性和关联关系，使用具体类可以确保测试覆盖真实的实体结构
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn(1);

        // 配置仓储返回账号列表
        $this->accountRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['isEnabled' => true])
            ->willReturn([$account])
        ;

        // 配置服务调用
        $this->syncService
            ->expects($this->once())
            ->method('sync')
            ->with(
                self::isInstanceOf(\DateTimeImmutable::class),
                self::isInstanceOf(\DateTimeImmutable::class),
                $account
            )
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([
            '--start-time' => '2023-01-01 00:00:00',
            '--end-time' => '2023-01-02 00:00:00',
        ]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);
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

    public function testExecuteWithException(): void
    {
        // 创建模拟账号
        // 必须使用具体类 Account 而不是接口的原因：
        // 1. Account 是具体的实体类，它是 Doctrine ORM 映射的数据库实体，没有抽象接口
        // 2. 测试需要验证与账号实体的特定属性访问，包括 getId() 方法的返回值
        // 3. 该实体包含复杂的属性和关联关系，使用具体类可以确保测试覆盖真实的实体结构
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn(1);

        // 配置仓储返回账号
        $this->accountRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['isEnabled' => true])
            ->willReturn([$account])
        ;

        // 配置服务抛出异常
        $this->syncService
            ->expects($this->once())
            ->method('sync')
            ->willThrowException(new \Exception('同步失败'))
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::FAILURE, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Failed to sync statistics for account 1: 同步失败', $output);
    }
}
