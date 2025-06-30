<?php

namespace TencentCloudSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Enum\SignPurpose;

class SignPurposeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, SignPurpose::SELF_USE->value);
        $this->assertSame(1, SignPurpose::OTHER_USE->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('自用', SignPurpose::SELF_USE->getLabel());
        $this->assertSame('他用', SignPurpose::OTHER_USE->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = SignPurpose::SELF_USE;
        $this->assertInstanceOf('Tourze\\EnumExtra\\Labelable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Itemable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Selectable', $enum);
    }
}
