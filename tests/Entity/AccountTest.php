<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Entity\Account;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): Account
    {
        return new Account();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'name' => ['name', '测试账号名称'],
            'secretId' => ['secretId', 'test-secret-id-12345'],
            'secretKey' => ['secretKey', 'test-secret-key-abcdef'],
            'valid' => ['valid', true],
            'createdBy' => ['createdBy', 'test-user'],
            'updatedBy' => ['updatedBy', 'another-user'],
            'createTime' => ['createTime', new \DateTimeImmutable()],
            'updateTime' => ['updateTime', new \DateTimeImmutable()],
        ];
    }

    public function testSettersWork(): void
    {
        // 测试setter方法功能正常
        $entity = $this->createEntity();
        $entity->setName('测试账号');
        $entity->setSecretId('test-id');
        $entity->setSecretKey('test-key');
        $entity->setValid(true);

        // 验证值设置正确
        $this->assertSame('测试账号', $entity->getName());
        $this->assertSame('test-id', $entity->getSecretId());
        $this->assertSame('test-key', $entity->getSecretKey());
        $this->assertTrue($entity->isValid());
    }

    public function testCanBeInstantiated(): void
    {
        $account = $this->createEntity();
        $this->assertNotNull($account);
    }

    public function testImplementsStringable(): void
    {
        $account = $this->createEntity();
        // Test that the string conversion works without error
        $stringValue = (string) $account;
        $this->assertNotEmpty($stringValue);
    }

    public function testDefaultValues(): void
    {
        $account = $this->createEntity();
        $this->assertEquals(0, $account->getId());
        $this->assertFalse($account->isValid());
    }

    public function testSettersReturnVoid(): void
    {
        $account = $this->createEntity();

        // void方法不应返回值，这里测试setter方法可以正常调用
        $account->setName('test');
        $account->setSecretId('test-id');
        $account->setSecretKey('test-key');
        $account->setValid(true);

        // 验证值被正确设置
        $this->assertSame('test', $account->getName());
        $this->assertSame('test-id', $account->getSecretId());
        $this->assertSame('test-key', $account->getSecretKey());
        $this->assertTrue($account->isValid());
    }
}
