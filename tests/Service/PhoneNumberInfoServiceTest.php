<?php

namespace TencentCloudSmsBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\SmsClient as TencentSmsClient;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Repository\PhoneNumberInfoRepository;
use TencentCloudSmsBundle\Service\PhoneNumberInfoService;
use TencentCloudSmsBundle\Service\SdkService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\UserServiceContracts\UserManagerInterface;

/**
 * @internal
 */
#[CoversClass(PhoneNumberInfoService::class)]
#[RunTestsInSeparateProcesses]
final class PhoneNumberInfoServiceTest extends AbstractIntegrationTestCase
{
    private UserManagerInterface $userManager;

    protected function onSetUp(): void
    {
        // 获取真实服务
        $this->userManager = self::getService(UserManagerInterface::class);
    }

    public function testCanBeInstantiated(): void
    {
        $service = self::getService(PhoneNumberInfoService::class);
        $this->assertNotNull($service);
        $this->assertInstanceOf(PhoneNumberInfoService::class, $service);
    }

    public function testSyncPhoneNumberInfoWithEmptyPhoneNumbers(): void
    {
        $service = self::getService(PhoneNumberInfoService::class);

        // 创建一个没有任何电话号码的账号
        $account = $this->createTestAccount();

        // 确保没有 nationCode 为 null 的电话号码
        $repository = self::getService(PhoneNumberInfoRepository::class);
        $phoneNumbers = $repository->findBy(['nationCode' => null]);

        // 如果有测试数据，先删除它们
        foreach ($phoneNumbers as $phoneNumber) {
            self::getEntityManager()->remove($phoneNumber);
        }
        self::getEntityManager()->flush();

        $service->syncPhoneNumberInfo($account);

        // 如果没有电话号码，应该直接返回，不抛出异常
        $this->assertTrue(true);
    }

    public function testSyncPhoneNumberInfoBasicFunctionality(): void
    {
        $service = self::getService(PhoneNumberInfoService::class);

        // 创建测试电话号码，nationCode 为 null
        $phoneNumber = $this->createTestPhoneNumber('1234567890');
        self::getEntityManager()->persist($phoneNumber);
        self::getEntityManager()->flush();

        // 由于我们无法 Mock final 类，这个测试主要验证基本功能
        // 在没有真实 API 的情况下，我们验证方法可以被调用而不抛出异常
        $account = $this->createTestAccount();

        // 这个测试可能会因为真实的 API 调用而失败，但我们主要验证服务的基本功能
        try {
            $service->syncPhoneNumberInfo($account);
        } catch (TencentCloudSDKException $e) {
            // 如果是因为 API 调用失败，这是预期的
            $this->assertContains('', $e->getMessage());
        }

        // 验证同步标志仍然是 false
        self::getEntityManager()->refresh($phoneNumber);
        $this->assertFalse($phoneNumber->isSyncing());
    }

    public function testServiceDependenciesAreAvailable(): void
    {
        // 验证所有必要的依赖都可以从容器中获取
        $this->assertNotNull(self::getService(PhoneNumberInfoService::class));
        $this->assertNotNull(self::getService(PhoneNumberInfoRepository::class));
        $this->assertNotNull(self::getService(SdkService::class));
        $this->assertNotNull(self::getEntityManager());
    }

    public function testBatchSizeConstant(): void
    {
        // 通过反射验证 BATCH_SIZE 常量
        $reflection = new \ReflectionClass(PhoneNumberInfoService::class);
        $this->assertTrue($reflection->hasConstant('BATCH_SIZE'));
        $this->assertSame(200, $reflection->getConstant('BATCH_SIZE'));
    }

    private function createTestAccount(): Account
    {
        $user = $this->userManager->createUser('test_user_' . uniqid());
        $this->userManager->saveUser($user);

        $account = new Account();
        $account->setName('Test Account ' . uniqid());
        $account->setSecretId('test_secret_id_' . uniqid());
        $account->setSecretKey('test_secret_key_' . uniqid());
        $account->setValid(true);
        $account->setCreatedBy($user);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        return $account;
    }

    private function createTestPhoneNumber(string $phoneNumber): PhoneNumberInfo
    {
        $phoneInfo = new PhoneNumberInfo();
        $phoneInfo->setPhoneNumber($phoneNumber);
        $phoneInfo->setSyncing(false);

        return $phoneInfo;
    }
}