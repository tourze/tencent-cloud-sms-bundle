<?php

namespace TencentCloudSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Enum\SmsType;

class SmsTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, SmsType::MAINLAND->value);
        $this->assertSame(1, SmsType::INTERNATIONAL->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('国内短信', SmsType::MAINLAND->getLabel());
        $this->assertSame('国际/港澳台短信', SmsType::INTERNATIONAL->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = SmsType::MAINLAND;
        $this->assertInstanceOf('Tourze\\EnumExtra\\Labelable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Itemable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Selectable', $enum);
    }
}
