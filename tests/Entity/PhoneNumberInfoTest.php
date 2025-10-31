<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(PhoneNumberInfo::class)]
final class PhoneNumberInfoTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): PhoneNumberInfo
    {
        $phoneNumberInfo = new PhoneNumberInfo();
        $phoneNumberInfo->setPhoneNumber('13800138000');

        return $phoneNumberInfo;
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'phoneNumber' => ['phoneNumber', '13800138000'],
            'nationCode' => ['nationCode', '86'],
            'isoCode' => ['isoCode', 'CN'],
            'isoName' => ['isoName', 'China'],
            'subscriberNumber' => ['subscriberNumber', '13800138000'],
            'fullNumber' => ['fullNumber', '+8613800138000'],
            'message' => ['message', 'Success'],
            'code' => ['code', '0'],
            'syncing' => ['syncing', false],
        ];
    }

    public function testSettersWork(): void
    {
        // 测试setter方法功能正常
        $entity = $this->createEntity();
        $entity->setPhoneNumber('13800138000');
        $entity->setNationCode('86');
        $entity->setIsoCode('CN');
        $entity->setIsoName('China');
        $entity->setSubscriberNumber('13800138000');
        $entity->setFullNumber('+8613800138000');
        $entity->setMessage('Success');
        $entity->setCode('0');
        $entity->setSyncing(true);

        // 验证值设置正确
        $this->assertSame('13800138000', $entity->getPhoneNumber());
        $this->assertSame('86', $entity->getNationCode());
        $this->assertSame('CN', $entity->getIsoCode());
        $this->assertTrue($entity->isSyncing());
    }

    public function testDefaultId(): void
    {
        $entity = $this->createEntity();
        $this->assertEquals(0, $entity->getId());
    }

    public function testSyncingGetterSetter(): void
    {
        $entity = $this->createEntity();
        $this->assertFalse($entity->isSyncing());

        $entity->setSyncing(true);
        $this->assertTrue($entity->isSyncing());

        $entity->setSyncing(false);
        $this->assertFalse($entity->isSyncing());
    }

    public function testNeedSyncWhenNationCodeIsNull(): void
    {
        $entity = $this->createEntity();
        $entity->setNationCode(null);
        $this->assertTrue($entity->needSync());
    }

    public function testDoesNotNeedSyncWhenNationCodeExists(): void
    {
        $entity = $this->createEntity();
        $entity->setNationCode('86');
        $this->assertFalse($entity->needSync());
    }

    public function testToString(): void
    {
        $entity = $this->createEntity();
        $entity->setPhoneNumber('13800138000');
        $entity->setNationCode('86');

        $this->assertEquals('PhoneInfo[13800138000:86]', (string) $entity);
    }

    public function testToStringWithNullNationCode(): void
    {
        $entity = $this->createEntity();
        $entity->setPhoneNumber('13800138000');
        $entity->setNationCode(null);

        $this->assertEquals('PhoneInfo[13800138000:]', (string) $entity);
    }

    public function testAllSettersAndGetters(): void
    {
        $entity = $this->createEntity();
        $entity->setPhoneNumber('13800138000');
        $entity->setNationCode('86');
        $entity->setIsoCode('CN');
        $entity->setIsoName('China');
        $entity->setSubscriberNumber('13800138000');
        $entity->setFullNumber('+8613800138000');
        $entity->setMessage('Success');
        $entity->setCode('0');

        $this->assertEquals('13800138000', $entity->getPhoneNumber());
        $this->assertEquals('86', $entity->getNationCode());
        $this->assertEquals('CN', $entity->getIsoCode());
        $this->assertEquals('China', $entity->getIsoName());
        $this->assertEquals('13800138000', $entity->getSubscriberNumber());
        $this->assertEquals('+8613800138000', $entity->getFullNumber());
        $this->assertEquals('Success', $entity->getMessage());
        $this->assertEquals('0', $entity->getCode());
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
}
