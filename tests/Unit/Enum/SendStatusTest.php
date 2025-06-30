<?php

namespace TencentCloudSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Enum\SendStatus;

class SendStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('success', SendStatus::SUCCESS->value);
        $this->assertSame('fail', SendStatus::FAIL->value);
        $this->assertSame('exceed', SendStatus::RATE_LIMIT_EXCEED->value);
        $this->assertSame('limit', SendStatus::PHONE_NUMBER_LIMIT->value);
        $this->assertSame('insufficient', SendStatus::INSUFFICIENT_PACKAGE->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('发送成功', SendStatus::SUCCESS->getLabel());
        $this->assertSame('发送失败', SendStatus::FAIL->getLabel());
        $this->assertSame('频率限制', SendStatus::RATE_LIMIT_EXCEED->getLabel());
        $this->assertSame('免打扰名单', SendStatus::PHONE_NUMBER_LIMIT->getLabel());
        $this->assertSame('余量不足', SendStatus::INSUFFICIENT_PACKAGE->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = SendStatus::SUCCESS;
        $this->assertInstanceOf('Tourze\\EnumExtra\\Labelable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Itemable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Selectable', $enum);
    }
}
