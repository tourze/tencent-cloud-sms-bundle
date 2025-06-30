<?php

namespace TencentCloudSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Enum\SignType;

class SignTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, SignType::COMPANY->value);
        $this->assertSame(1, SignType::APP->value);
        $this->assertSame(2, SignType::WEBSITE->value);
        $this->assertSame(3, SignType::WECHAT->value);
        $this->assertSame(4, SignType::TRADEMARK->value);
        $this->assertSame(5, SignType::GOVERNMENT->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('公司', SignType::COMPANY->getLabel());
        $this->assertSame('APP', SignType::APP->getLabel());
        $this->assertSame('网站', SignType::WEBSITE->getLabel());
        $this->assertSame('公众号', SignType::WECHAT->getLabel());
        $this->assertSame('商标', SignType::TRADEMARK->getLabel());
        $this->assertSame('政府/机关事业单位/其他机构', SignType::GOVERNMENT->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = SignType::COMPANY;
        $this->assertInstanceOf('Tourze\\EnumExtra\\Labelable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Itemable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Selectable', $enum);
    }
}
