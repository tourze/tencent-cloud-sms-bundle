<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\Embed\CallbackStatistics;
use TencentCloudSmsBundle\Entity\Embed\PackageStatistics;
use TencentCloudSmsBundle\Entity\Embed\SendStatistics;
use TencentCloudSmsBundle\Entity\SmsStatistics;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(SmsStatistics::class)]
final class SmsStatisticsTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): SmsStatistics
    {
        $statistics = new SmsStatistics();

        // 设置必需的属性以避免构造函数错误
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $statistics->setHour(new \DateTimeImmutable());
        $statistics->setAccount($account);

        return $statistics;
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'hour' => ['hour', new \DateTimeImmutable()],
            'createTime' => ['createTime', new \DateTimeImmutable()],
            'updateTime' => ['updateTime', new \DateTimeImmutable()],
        ];
    }

    public function testSettersWork(): void
    {
        // 测试setter方法功能正常
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $entity = $this->createEntity();
        $hour = new \DateTimeImmutable();
        $entity->setHour($hour);
        $entity->setAccount($account);

        // 验证值设置正确
        $this->assertSame($hour, $entity->getHour());
        $this->assertSame($account, $entity->getAccount());
    }

    public function testCanBeInstantiated(): void
    {
        $entity = $this->createEntity();
        $this->assertNotNull($entity);
    }

    public function testImplementsStringable(): void
    {
        $entity = $this->createEntity();
        // Test that the string conversion works without error
        $stringValue = (string) $entity;
        $this->assertNotEmpty($stringValue);
    }

    public function testHourGetterSetter(): void
    {
        $entity = $this->createEntity();
        $hour = new \DateTimeImmutable();
        $entity->setHour($hour);
        $this->assertSame($hour, $entity->getHour());
    }

    public function testAccountGetterSetter(): void
    {
        $entity = $this->createEntity();
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $entity->setAccount($account);
        $this->assertSame($account, $entity->getAccount());
    }

    public function testEmbeddedObjects(): void
    {
        $entity = $this->createEntity();

        // 测试内嵌对象不为空
        $this->assertNotNull($entity->getSendStatistics());
        $this->assertNotNull($entity->getCallbackStatistics());
        $this->assertNotNull($entity->getPackageStatistics());

        // 测试内嵌对象的类型
        $this->assertInstanceOf(SendStatistics::class, $entity->getSendStatistics());
        $this->assertInstanceOf(CallbackStatistics::class, $entity->getCallbackStatistics());
        $this->assertInstanceOf(PackageStatistics::class, $entity->getPackageStatistics());
    }
}
