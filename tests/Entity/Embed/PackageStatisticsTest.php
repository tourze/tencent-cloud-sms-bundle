<?php

namespace TencentCloudSmsBundle\Tests\Entity\Embed;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\Embed\PackageStatistics;

/**
 * @internal
 */
#[CoversClass(PackageStatistics::class)]
final class PackageStatisticsTest extends TestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): PackageStatistics
    {
        return new PackageStatistics();
    }

    #[DataProvider('propertiesProvider')]
    public function testGettersAndSetters(string $property, int $value): void
    {
        $entity = $this->createEntity();

        switch ($property) {
            case 'packageAmount':
                $entity->setPackageAmount($value);
                $this->assertSame($value, $entity->getPackageAmount(), 'Getter should return the value set by setter');
                break;
            case 'usedAmount':
                $entity->setUsedAmount($value);
                $this->assertSame($value, $entity->getUsedAmount(), 'Getter should return the value set by setter');
                break;
            default:
                self::fail("Unknown property: {$property}");
        }
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{0: string, 1: int}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'packageAmount' => ['packageAmount', 1000],
            'usedAmount' => ['usedAmount', 500],
        ];
    }

    public function testSettersWork(): void
    {
        $entity = $this->createEntity();
        $entity->setPackageAmount(1000);
        $entity->setUsedAmount(500);

        // 验证值设置正确
        $this->assertSame(1000, $entity->getPackageAmount());
        $this->assertSame(500, $entity->getUsedAmount());
    }

    public function testCanBeInstantiated(): void
    {
        $entity = $this->createEntity();
        $this->assertNotNull($entity);
    }
}
