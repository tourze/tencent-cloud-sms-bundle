<?php

namespace TencentCloudSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use TencentCloudSmsBundle\Repository\SmsRecipientRepository;
use TencentCloudSmsBundle\Service\SmsClient;
use TencentCloudSmsBundle\Service\SmsStatusService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SmsStatusService::class)]
#[RunTestsInSeparateProcesses]
final class SmsStatusServiceTest extends AbstractIntegrationTestCase
{
    private SmsClient|MockObject $smsClient;

    private EntityManagerInterface|MockObject $mockEntityManager;

    private SmsRecipientRepository|MockObject $recipientRepository;

    private LoggerInterface|MockObject $logger;

    private SmsStatusService $service;

    protected function onSetUp(): void
    {
        /*
         * 使用 SmsClient 具体类是必要的，因为：
         * 1) 该类是内部实现，没有对应的接口定义
         * 2) 测试需要模拟其内部方法行为
         * 3) 替代方案是创建接口，但会增加不必要的复杂性
         */
        $this->smsClient = $this->createMock(SmsClient::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        /*
         * 使用 SmsRecipientRepository 具体类是必要的，因为：
         * 1) Repository 类通常继承自 EntityRepository，没有独立接口
         * 2) 测试需要模拟其特定的查询方法
         * 3) 创建接口会违反 Repository 模式的惯例
         */
        $this->recipientRepository = $this->createMock(SmsRecipientRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        // 直接创建SmsStatusService实例进行测试
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
        $this->service = new SmsStatusService($this->smsClient, $this->mockEntityManager, $this->recipientRepository, $this->logger);
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertNotNull($this->service);
    }

    public function testSyncStatus(): void
    {
        // 设置Mock行为 - 模拟查找需要同步的记录
        $this->recipientRepository->expects($this->once())
            ->method('findNeedSyncStatus')
            ->with(self::callback(function (\DateTime $dateTime) {
                // 验证传入的时间大约是1小时前
                $diff = (new \DateTime())->getTimestamp() - $dateTime->getTimestamp();

                return $diff >= 3590 && $diff <= 3610; // 允许10秒误差
            }))
            ->willReturn([])
        ;

        // 执行测试
        $this->service->syncStatus();
    }

    public function testSyncUnknownStatus(): void
    {
        $limit = 50;

        // 设置Mock行为 - 模拟查找未知状态的记录
        $this->recipientRepository->expects($this->once())
            ->method('findUnknownStatus')
            ->with($limit)
            ->willReturn([])
        ;

        // 执行测试
        $this->service->syncUnknownStatus($limit);
    }

    public function testSyncUnknownStatusWithDefaultLimit(): void
    {
        // 设置Mock行为 - 模拟查找未知状态的记录，使用默认限制
        $this->recipientRepository->expects($this->once())
            ->method('findUnknownStatus')
            ->with(100) // 默认限制是100
            ->willReturn([])
        ;

        // 执行测试
        $this->service->syncUnknownStatus();
    }
}
