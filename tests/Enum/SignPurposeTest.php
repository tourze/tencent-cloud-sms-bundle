<?php

namespace TencentCloudSmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Enum\SignPurpose;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(SignPurpose::class)]
final class SignPurposeTest extends AbstractEnumTestCase
{
    public function testValuesAreUnique(): void
    {
        $values = array_map(fn ($case) => $case->value, SignPurpose::cases());
        $this->assertSame(count($values), count(array_unique($values)), '所有枚举的 value 必须是唯一的。');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), SignPurpose::cases());
        $this->assertSame(count($labels), count(array_unique($labels)), '所有枚举的 label 必须是唯一的。');
    }

    public function testToArray(): void
    {
        $arrayData = SignPurpose::SELF_USE->toArray();
        $this->assertArrayHasKey('value', $arrayData);
        $this->assertArrayHasKey('label', $arrayData);
        $this->assertSame(0, $arrayData['value']);
        $this->assertSame('自用', $arrayData['label']);
    }
}
