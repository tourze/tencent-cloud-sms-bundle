<?php

namespace TencentCloudSmsBundle\Tests\Enum;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Enum\SendStatus;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(SendStatus::class)]
final class SendStatusTest extends AbstractEnumTestCase
{
    public function testValuesAreUnique(): void
    {
        $values = array_map(fn ($case) => $case->value, SendStatus::cases());
        $this->assertSame(count($values), count(array_unique($values)), '所有枚举的 value 必须是唯一的。');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn ($case) => $case->getLabel(), SendStatus::cases());
        $this->assertSame(count($labels), count(array_unique($labels)), '所有枚举的 label 必须是唯一的。');
    }

    public function testToArray(): void
    {
        $arrayData = SendStatus::SUCCESS->toArray();
        $this->assertArrayHasKey('value', $arrayData);
        $this->assertArrayHasKey('label', $arrayData);
        $this->assertSame('success', $arrayData['value']);
        $this->assertSame('发送成功', $arrayData['label']);
    }
}
