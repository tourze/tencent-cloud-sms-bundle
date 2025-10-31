<?php

namespace TencentCloudSmsBundle\Tests\Entity\Embed;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\Embed\SendStatistics;

/**
 * @internal
 */
#[CoversClass(SendStatistics::class)]
final class SendStatisticsTest extends TestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): SendStatistics
    {
        return new SendStatistics();
    }

    #[DataProvider('propertiesProvider')]
    public function testGettersAndSetters(string $property, int $value): void
    {
        $entity = $this->createEntity();

        switch ($property) {
            case 'requestCount':
                $entity->setRequestCount($value);
                $this->assertSame($value, $entity->getRequestCount(), 'Getter should return the value set by setter');
                break;
            case 'requestSuccessCount':
                $entity->setRequestSuccessCount($value);
                $this->assertSame($value, $entity->getRequestSuccessCount(), 'Getter should return the value set by setter');
                break;
            case 'requestFailCount':
                $entity->setRequestFailCount($value);
                $this->assertSame($value, $entity->getRequestFailCount(), 'Getter should return the value set by setter');
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
            'requestCount' => ['requestCount', 100],
            'requestSuccessCount' => ['requestSuccessCount', 95],
            'requestFailCount' => ['requestFailCount', 5],
        ];
    }

    public function testFluidInterface(): void
    {
        $entity = $this->createEntity();
        $entity->setRequestCount(100);
        $entity->setRequestSuccessCount(95);
        $entity->setRequestFailCount(5);

        // 验证值设置正确
        $this->assertSame(100, $entity->getRequestCount());
        $this->assertSame(95, $entity->getRequestSuccessCount());
        $this->assertSame(5, $entity->getRequestFailCount());
    }

    public function testCanBeInstantiated(): void
    {
        $entity = $this->createEntity();
        $this->assertNotNull($entity);
    }
}
