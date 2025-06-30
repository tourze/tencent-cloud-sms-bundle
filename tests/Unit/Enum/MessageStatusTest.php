<?php

namespace TencentCloudSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Enum\MessageStatus;

class MessageStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, MessageStatus::SENDING->value);
        $this->assertSame(1, MessageStatus::SUCCESS->value);
        $this->assertSame(2, MessageStatus::FAILED->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('发送中', MessageStatus::SENDING->getLabel());
        $this->assertSame('发送成功', MessageStatus::SUCCESS->getLabel());
        $this->assertSame('发送失败', MessageStatus::FAILED->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = MessageStatus::SENDING;
        $this->assertInstanceOf('Tourze\\EnumExtra\\Labelable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Itemable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Selectable', $enum);
    }
}
