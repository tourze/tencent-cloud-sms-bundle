<?php

namespace TencentCloudSmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Enum\DocumentType;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DocumentType::class)]
final class DocumentTypeTest extends AbstractEnumTestCase
{
    public function testValuesAreUnique(): void
    {
        $values = array_map(fn ($case) => $case->value, DocumentType::cases());
        $this->assertSame(count($values), count(array_unique($values)), '所有枚举的 value 必须是唯一的。');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), DocumentType::cases());
        $this->assertSame(count($labels), count(array_unique($labels)), '所有枚举的 label 必须是唯一的。');
    }

    public function testToArray(): void
    {
        $arrayData = DocumentType::THREE_IN_ONE->toArray();
        $this->assertArrayHasKey('value', $arrayData);
        $this->assertArrayHasKey('label', $arrayData);
        $this->assertSame(0, $arrayData['value']);
        $this->assertSame('三证合一', $arrayData['label']);
    }
}
