<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsRecipient;

class SmsRecipientTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $recipient = new SmsRecipient();
        $this->assertInstanceOf(SmsRecipient::class, $recipient);
    }

    public function testImplementsStringable(): void
    {
        $recipient = new SmsRecipient();
        
        // 创建 PhoneNumberInfo 对象
        $phoneNumberInfo = new \TencentCloudSmsBundle\Entity\PhoneNumberInfo();
        $phoneNumberInfo->setPhoneNumber('13800138000')
            ->setNationCode('+86');
        $recipient->setPhoneNumber($phoneNumberInfo); // 设置手机号以确保 __toString 返回非空字符串
        
        $this->assertInstanceOf(\Stringable::class, $recipient);
        // Test that the string conversion works without error
        $stringValue = (string) $recipient;
        $this->assertNotEmpty($stringValue);
    }
}
