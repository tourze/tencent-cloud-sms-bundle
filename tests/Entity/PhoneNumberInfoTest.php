<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;

class PhoneNumberInfoTest extends TestCase
{
    private PhoneNumberInfo $phoneNumberInfo;

    public function testPhoneNumberGetterSetter(): void
    {
        $phoneNumber = '13800138000';
        $result = $this->phoneNumberInfo->setPhoneNumber($phoneNumber);

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertEquals($phoneNumber, $this->phoneNumberInfo->getPhoneNumber());
    }

    public function testNationCodeGetterSetter(): void
    {
        $nationCode = '86';
        $result = $this->phoneNumberInfo->setNationCode($nationCode);

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertEquals($nationCode, $this->phoneNumberInfo->getNationCode());
    }

    public function testIsoCodeGetterSetter(): void
    {
        $isoCode = 'CN';
        $result = $this->phoneNumberInfo->setIsoCode($isoCode);

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertEquals($isoCode, $this->phoneNumberInfo->getIsoCode());
    }

    public function testIsoNameGetterSetter(): void
    {
        $isoName = 'China';
        $result = $this->phoneNumberInfo->setIsoName($isoName);

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertEquals($isoName, $this->phoneNumberInfo->getIsoName());
    }

    public function testSubscriberNumberGetterSetter(): void
    {
        $subscriberNumber = '13800138000';
        $result = $this->phoneNumberInfo->setSubscriberNumber($subscriberNumber);

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertEquals($subscriberNumber, $this->phoneNumberInfo->getSubscriberNumber());
    }

    public function testFullNumberGetterSetter(): void
    {
        $fullNumber = '+8613800138000';
        $result = $this->phoneNumberInfo->setFullNumber($fullNumber);

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertEquals($fullNumber, $this->phoneNumberInfo->getFullNumber());
    }

    public function testMessageGetterSetter(): void
    {
        $message = 'Success';
        $result = $this->phoneNumberInfo->setMessage($message);

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertEquals($message, $this->phoneNumberInfo->getMessage());
    }

    public function testCodeGetterSetter(): void
    {
        $code = '0';
        $result = $this->phoneNumberInfo->setCode($code);

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertEquals($code, $this->phoneNumberInfo->getCode());
    }

    public function testDefaultId(): void
    {
        $this->assertEquals(0, $this->phoneNumberInfo->getId());
    }

    public function testSyncingGetterSetter(): void
    {
        $this->assertFalse($this->phoneNumberInfo->isSyncing());

        $result = $this->phoneNumberInfo->setSyncing(true);

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertTrue($this->phoneNumberInfo->isSyncing());

        $this->phoneNumberInfo->setSyncing(false);
        $this->assertFalse($this->phoneNumberInfo->isSyncing());
    }

    public function testNeedSyncWhenNationCodeIsNull(): void
    {
        $this->phoneNumberInfo->setNationCode(null);
        $this->assertTrue($this->phoneNumberInfo->needSync());
    }

    public function testDoesNotNeedSyncWhenNationCodeExists(): void
    {
        $this->phoneNumberInfo->setNationCode('86');
        $this->assertFalse($this->phoneNumberInfo->needSync());
    }

    public function testToString(): void
    {
        $this->phoneNumberInfo
            ->setPhoneNumber('13800138000')
            ->setNationCode('86');

        $this->assertEquals('PhoneInfo[13800138000:86]', (string) $this->phoneNumberInfo);
    }

    public function testToStringWithNullNationCode(): void
    {
        $this->phoneNumberInfo
            ->setPhoneNumber('13800138000')
            ->setNationCode(null);

        $this->assertEquals('PhoneInfo[13800138000:]', (string) $this->phoneNumberInfo);
    }

    public function testChainedSetters(): void
    {
        $result = $this->phoneNumberInfo
            ->setPhoneNumber('13800138000')
            ->setNationCode('86')
            ->setIsoCode('CN')
            ->setIsoName('China')
            ->setSubscriberNumber('13800138000')
            ->setFullNumber('+8613800138000')
            ->setMessage('Success')
            ->setCode('0');

        $this->assertSame($this->phoneNumberInfo, $result);
        $this->assertEquals('13800138000', $this->phoneNumberInfo->getPhoneNumber());
        $this->assertEquals('86', $this->phoneNumberInfo->getNationCode());
        $this->assertEquals('CN', $this->phoneNumberInfo->getIsoCode());
        $this->assertEquals('China', $this->phoneNumberInfo->getIsoName());
        $this->assertEquals('13800138000', $this->phoneNumberInfo->getSubscriberNumber());
        $this->assertEquals('+8613800138000', $this->phoneNumberInfo->getFullNumber());
        $this->assertEquals('Success', $this->phoneNumberInfo->getMessage());
        $this->assertEquals('0', $this->phoneNumberInfo->getCode());
    }

    protected function setUp(): void
    {
        $this->phoneNumberInfo = new PhoneNumberInfo();
    }
}
