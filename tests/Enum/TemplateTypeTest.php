<?php

namespace TencentCloudSmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Enum\TemplateType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(TemplateType::class)]
final class TemplateTypeTest extends AbstractEnumTestCase
{
    public function testValuesAreUnique(): void
    {
        $values = array_map(fn ($case) => $case->value, TemplateType::cases());
        $this->assertSame(count($values), count(array_unique($values)), '所有枚举的 value 必须是唯一的。');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), TemplateType::cases());
        $this->assertSame(count($labels), count(array_unique($labels)), '所有枚举的 label 必须是唯一的。');
    }

    public function testToArray(): void
    {
        $arrayData = TemplateType::REGULAR->toArray();
        $this->assertArrayHasKey('value', $arrayData);
        $this->assertArrayHasKey('label', $arrayData);
        $this->assertSame(0, $arrayData['value']);
        $this->assertSame('普通短信', $arrayData['label']);
    }
}
