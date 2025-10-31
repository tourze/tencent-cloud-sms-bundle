<?php

namespace TencentCloudSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\Repository\SmsSignatureRepository;
use TencentCloudSmsBundle\Repository\SmsTemplateRepository;
use TencentCloudSmsBundle\Service\SmsClient;
use TencentCloudSmsBundle\Service\StatusSyncService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(StatusSyncService::class)]
#[RunTestsInSeparateProcesses]
final class StatusSyncServiceTest extends AbstractIntegrationTestCase
{
    private StatusSyncService $statusSyncService;

    private SmsClient&MockObject $mockSmsClient;

    private EntityManagerInterface&MockObject $mockEntityManager;

    private SmsSignatureRepository&MockObject $mockSignatureRepository;

    private SmsTemplateRepository&MockObject $mockTemplateRepository;

    private LoggerInterface&MockObject $mockLogger;

    protected function onSetUp(): void
    {
        $this->mockSmsClient = $this->createMock(SmsClient::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockSignatureRepository = $this->createMock(SmsSignatureRepository::class);
        $this->mockTemplateRepository = $this->createMock(SmsTemplateRepository::class);
        $this->mockLogger = $this->createMock(LoggerInterface::class);

        // 直接创建StatusSyncService实例进行单元测试
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
        $this->statusSyncService = new StatusSyncService(
            $this->mockSmsClient,
            $this->mockEntityManager,
            $this->mockSignatureRepository,
            $this->mockTemplateRepository,
            $this->mockLogger
        );
    }

    public function testSyncSignaturesWithNoSignatures(): void
    {
        $this->mockSignatureRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([])
        ;

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->statusSyncService->syncSignatures();
    }

    public function testSyncSignaturesWithSignatureWithoutAccount(): void
    {
        $signature = $this->createMock(SmsSignature::class);
        $signature->expects($this->once())
            ->method('getAccount')
            ->willReturn(null)
        ;

        $this->mockSignatureRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$signature])
        ;

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->statusSyncService->syncSignatures();
    }

    public function testSyncSignaturesWithException(): void
    {
        $account = $this->createMock(Account::class);
        $signature = $this->createMock(SmsSignature::class);

        $signature->expects($this->once())
            ->method('getAccount')
            ->willReturn($account)
        ;

        $signature->expects($this->once())
            ->method('setSyncing')
            ->with(false)
        ;

        $this->mockSmsClient
            ->expects($this->once())
            ->method('create')
            ->with($account)
            ->willThrowException(new TencentCloudSDKException('SDK Error'))
        ;

        $this->mockSignatureRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$signature])
        ;

        $this->mockLogger
            ->expects($this->once())
            ->method('error')
            ->with('签名状态同步失败', self::anything())
        ;

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->statusSyncService->syncSignatures();
    }

    public function testSyncTemplatesWithNoTemplates(): void
    {
        $this->mockTemplateRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([])
        ;

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->statusSyncService->syncTemplates();
    }

    public function testSyncTemplatesWithTemplateWithoutAccount(): void
    {
        $template = $this->createMock(SmsTemplate::class);
        $template->expects($this->once())
            ->method('getAccount')
            ->willReturn(null)
        ;

        $this->mockTemplateRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$template])
        ;

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->statusSyncService->syncTemplates();
    }

    public function testSyncTemplatesWithException(): void
    {
        $account = $this->createMock(Account::class);
        $template = $this->createMock(SmsTemplate::class);

        $template->expects($this->once())
            ->method('getAccount')
            ->willReturn($account)
        ;

        $template->expects($this->once())
            ->method('setSyncing')
            ->with(false)
        ;

        $this->mockSmsClient
            ->expects($this->once())
            ->method('create')
            ->with($account)
            ->willThrowException(new TencentCloudSDKException('SDK Error'))
        ;

        $this->mockTemplateRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$template])
        ;

        $this->mockLogger
            ->expects($this->once())
            ->method('error')
            ->with('模板状态同步失败', self::anything())
        ;

        $this->mockEntityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $this->statusSyncService->syncTemplates();
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(StatusSyncService::class, $this->statusSyncService);
    }
}
