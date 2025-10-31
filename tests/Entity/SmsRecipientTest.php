<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\SendStatus;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(SmsRecipient::class)]
final class SmsRecipientTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): SmsRecipient
    {
        $recipient = new SmsRecipient();

        // 设置必需的属性以避免构造函数错误
        $phoneNumberInfo = new PhoneNumberInfo();
        $phoneNumberInfo->setPhoneNumber('13800138000');
        $recipient->setPhoneNumber($phoneNumberInfo);

        return $recipient;
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'status' => ['status', SendStatus::SUCCESS],
            'serialNo' => ['serialNo', 'serial-123456'],
            'fee' => ['fee', 1],
            'code' => ['code', '0'],
            'statusMessage' => ['statusMessage', 'Success'],
            'createTime' => ['createTime', new \DateTimeImmutable()],
            'updateTime' => ['updateTime', new \DateTimeImmutable()],
        ];
    }

    public function testSettersWork(): void
    {
        // 测试setter方法功能正常
        $phoneNumberInfo = new PhoneNumberInfo();
        $phoneNumberInfo->setPhoneNumber('13800138000');

        $entity = $this->createEntity();
        $entity->setStatus(SendStatus::SUCCESS);
        $entity->setSerialNo('serial-123456');
        $entity->setFee(1);
        $entity->setCode('0');
        $entity->setStatusMessage('Success');

        // 验证值设置正确
        $this->assertSame(SendStatus::SUCCESS, $entity->getStatus());
        $this->assertSame('serial-123456', $entity->getSerialNo());
        $this->assertSame(1, $entity->getFee());
        $this->assertSame('0', $entity->getCode());
        $this->assertSame('Success', $entity->getStatusMessage());
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

    public function testStatusGetterSetter(): void
    {
        $entity = $this->createEntity();

        // 默认应为null
        $this->assertNull($entity->getStatus());

        // 设置为SUCCESS
        $entity->setStatus(SendStatus::SUCCESS);
        $this->assertEquals(SendStatus::SUCCESS, $entity->getStatus());

        // 设置为FAIL
        $entity->setStatus(SendStatus::FAIL);
        $this->assertEquals(SendStatus::FAIL, $entity->getStatus());
    }

    public function testSerialNoGetterSetter(): void
    {
        $entity = $this->createEntity();
        $serialNo = 'serial-123456';
        $entity->setSerialNo($serialNo);
        $this->assertEquals($serialNo, $entity->getSerialNo());
    }

    public function testFeeGetterSetter(): void
    {
        $entity = $this->createEntity();
        $fee = 1;
        $entity->setFee($fee);
        $this->assertEquals($fee, $entity->getFee());
    }

    public function testCodeGetterSetter(): void
    {
        $entity = $this->createEntity();
        $code = '0';
        $entity->setCode($code);
        $this->assertEquals($code, $entity->getCode());
    }

    public function testStatusMessageGetterSetter(): void
    {
        $entity = $this->createEntity();
        $statusMessage = 'Success';
        $entity->setStatusMessage($statusMessage);
        $this->assertEquals($statusMessage, $entity->getStatusMessage());
    }

    public function testPhoneNumberGetterSetter(): void
    {
        $entity = $this->createEntity();
        $phoneNumberInfo = new PhoneNumberInfo();
        $phoneNumberInfo->setPhoneNumber('13800138000');

        $entity->setPhoneNumber($phoneNumberInfo);
        $this->assertSame($phoneNumberInfo, $entity->getPhoneNumber());
    }
}
