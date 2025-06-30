<?php

namespace TencentCloudSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Enum\TemplateReviewStatus;

class TemplateReviewStatusTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, TemplateReviewStatus::APPROVED->value);
        $this->assertSame(1, TemplateReviewStatus::REVIEWING->value);
        $this->assertSame(2, TemplateReviewStatus::PENDING->value);
        $this->assertSame(-1, TemplateReviewStatus::REJECTED->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('已生效', TemplateReviewStatus::APPROVED->getLabel());
        $this->assertSame('审核中', TemplateReviewStatus::REVIEWING->getLabel());
        $this->assertSame('待生效', TemplateReviewStatus::PENDING->getLabel());
        $this->assertSame('未通过', TemplateReviewStatus::REJECTED->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = TemplateReviewStatus::APPROVED;
        $this->assertInstanceOf('Tourze\\EnumExtra\\Labelable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Itemable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Selectable', $enum);
    }
}
