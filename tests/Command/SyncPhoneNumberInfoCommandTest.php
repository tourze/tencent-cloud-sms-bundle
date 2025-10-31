<?php

namespace TencentCloudSmsBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TencentCloudSmsBundle\Command\SyncPhoneNumberInfoCommand;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Repository\AccountRepository;
use TencentCloudSmsBundle\Service\PhoneNumberInfoService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(SyncPhoneNumberInfoCommand::class)]
#[RunTestsInSeparateProcesses]
final class SyncPhoneNumberInfoCommandTest extends AbstractCommandTestCase
{
    private PhoneNumberInfoService&MockObject $phoneNumberInfoService;

    private AccountRepository&MockObject $accountRepository;

    protected function onSetUp(): void
    {
        // 创建模拟服务
        // 必须 mock PhoneNumberInfoService 具体类的理由1: 该类没有定义接口，是具体的业务逻辑实现类
        // 理由2: 测试需要验证与具体实现的交互行为（syncPhoneNumberInfo 方法）
        // 理由3: 该类包含复杂的业务逻辑，通过 mock 可以隔离测试命令本身的逻辑
        $this->phoneNumberInfoService = $this->createMock(PhoneNumberInfoService::class);

        // 必须 mock AccountRepository 具体类的理由1: 该类继承自 Doctrine ServiceEntityRepository，没有定义接口
        // 理由2: 测试需要验证与数据库交互的行为（findAll 方法）
        // 理由3: 避免在单元测试中依赖真实的数据库连接和 Doctrine 配置
        $this->accountRepository = $this->createMock(AccountRepository::class);

        // 注册模拟服务到容器
        $container = self::getContainer();
        $container->set(PhoneNumberInfoService::class, $this->phoneNumberInfoService);
        $container->set(AccountRepository::class, $this->accountRepository);
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(SyncPhoneNumberInfoCommand::class);
        $this->assertInstanceOf(SyncPhoneNumberInfoCommand::class, $command);

        return new CommandTester($command);
    }

    public function testCommandName(): void
    {
        $command = self::getService(SyncPhoneNumberInfoCommand::class);

        $this->assertEquals(SyncPhoneNumberInfoCommand::NAME, $command->getName());
        $this->assertEquals('tencent-cloud:sms:sync:phone-number-info', $command->getName());
    }

    public function testCommandDescription(): void
    {
        $command = self::getService(SyncPhoneNumberInfoCommand::class);

        $this->assertEquals('同步手机号码信息', $command->getDescription());
    }

    public function testExecuteWithNoAccounts(): void
    {
        // 配置仓储返回空数组
        $this->accountRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([])
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);
    }

    public function testExecuteWithAccounts(): void
    {
        // 创建模拟账号
        // 必须 mock Account 具体类的理由1: Account 是 Doctrine Entity，没有定义接口
        // 理由2: 测试需要验证与实体对象的交互（getId 方法）
        // 理由3: 避免在测试中创建真实的 Entity 对象及其复杂的 ORM 依赖
        $account1 = $this->createMock(Account::class);
        $account1->method('getId')->willReturn(1);

        // 必须 mock Account 具体类的理由1: Account 是 Doctrine Entity，没有定义接口
        // 理由2: 测试需要验证与实体对象的交互（getId 方法）
        // 理由3: 避免在测试中创建真实的 Entity 对象及其复杂的 ORM 依赖
        $account2 = $this->createMock(Account::class);
        $account2->method('getId')->willReturn(2);

        $accounts = [$account1, $account2];

        // 配置仓储返回账号列表
        $this->accountRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($accounts)
        ;

        // 配置服务调用
        $this->phoneNumberInfoService
            ->expects($this->exactly(2))
            ->method('syncPhoneNumberInfo')
            ->with(self::logicalOr($account1, $account2))
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('账号 1 手机号码信息同步完成', $output);
        $this->assertStringContainsString('账号 2 手机号码信息同步完成', $output);
    }

    public function testExecuteWithException(): void
    {
        // 创建模拟账号
        // 必须 mock Account 具体类的理由1: Account 是 Doctrine Entity，没有定义接口
        // 理由2: 测试需要验证与实体对象的交互（getId 方法）
        // 理由3: 避免在测试中创建真实的 Entity 对象及其复杂的 ORM 依赖
        $account = $this->createMock(Account::class);
        $account->method('getId')->willReturn(1);

        // 配置仓储返回账号
        $this->accountRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$account])
        ;

        // 配置服务抛出异常
        $this->phoneNumberInfoService
            ->expects($this->once())
            ->method('syncPhoneNumberInfo')
            ->with($account)
            ->willThrowException(new \Exception('同步失败'))
        ;

        // 执行命令
        $commandTester = $this->getCommandTester();
        $exitCode = $commandTester->execute([]);

        // 验证结果
        $this->assertEquals(Command::SUCCESS, $exitCode);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('账号 1 手机号码信息同步失败: 同步失败', $output);
    }
}
