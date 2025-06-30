<?php

namespace TencentCloudSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Enum\SignReviewStatus;

class SignReviewStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, SignReviewStatus::APPROVED->value);
        $this->assertSame(1, SignReviewStatus::REVIEWING->value);
        $this->assertSame(2, SignReviewStatus::PENDING->value);
        $this->assertSame(-1, SignReviewStatus::REJECTED->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('已生效', SignReviewStatus::APPROVED->getLabel());
        $this->assertSame('审核中', SignReviewStatus::REVIEWING->getLabel());
        $this->assertSame('待生效', SignReviewStatus::PENDING->getLabel());
        $this->assertSame('未通过', SignReviewStatus::REJECTED->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = SignReviewStatus::APPROVED;
        $this->assertInstanceOf('Tourze\\EnumExtra\\Labelable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Itemable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Selectable', $enum);
    }
}
