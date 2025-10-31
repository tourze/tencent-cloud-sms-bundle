<?php

namespace TencentCloudSmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Enum\SignReviewStatus;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(SignReviewStatus::class)]
final class SignReviewStatusTest extends AbstractEnumTestCase
{
    public function testValuesAreUnique(): void
    {
        $values = array_map(fn ($case) => $case->value, SignReviewStatus::cases());
        $this->assertSame(count($values), count(array_unique($values)), '所有枚举的 value 必须是唯一的。');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), SignReviewStatus::cases());
        $this->assertSame(count($labels), count(array_unique($labels)), '所有枚举的 label 必须是唯一的。');
    }

    public function testToArray(): void
    {
        $arrayData = SignReviewStatus::APPROVED->toArray();
        $this->assertArrayHasKey('value', $arrayData);
        $this->assertArrayHasKey('label', $arrayData);
        $this->assertSame(0, $arrayData['value']);
        $this->assertSame('已生效', $arrayData['label']);
    }
}
