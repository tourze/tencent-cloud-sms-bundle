<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsSignature;

class SmsSignatureTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $signature = new SmsSignature();
        $this->assertInstanceOf(SmsSignature::class, $signature);
    }

    public function testImplementsStringable(): void
    {
        $signature = new SmsSignature();
        $signature->setSignName('测试签名'); // 设置签名名称以确保 __toString 返回非空字符串
        
        $this->assertInstanceOf(\Stringable::class, $signature);
        // Test that the string conversion works without error
        $stringValue = (string) $signature;
        $this->assertNotEmpty($stringValue);
    }
}
