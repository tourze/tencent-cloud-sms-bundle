<?php

namespace TencentCloudSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Enum\TemplateType;

class TemplateTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, TemplateType::REGULAR->value);
        $this->assertSame(1, TemplateType::MARKETING->value);
        $this->assertSame(2, TemplateType::NOTIFICATION->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('普通短信', TemplateType::REGULAR->getLabel());
        $this->assertSame('营销短信', TemplateType::MARKETING->getLabel());
        $this->assertSame('通知类短信', TemplateType::NOTIFICATION->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = TemplateType::REGULAR;
        $this->assertInstanceOf('Tourze\\EnumExtra\\Labelable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Itemable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Selectable', $enum);
    }
}
