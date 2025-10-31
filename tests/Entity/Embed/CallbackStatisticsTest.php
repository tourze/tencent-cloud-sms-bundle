<?php

namespace TencentCloudSmsBundle\Tests\Entity\Embed;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\Embed\CallbackStatistics;

/**
 * @internal
 */
#[CoversClass(CallbackStatistics::class)]
final class CallbackStatisticsTest extends TestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): CallbackStatistics
    {
        return new CallbackStatistics();
    }

    #[DataProvider('propertiesProvider')]
    public function testGettersAndSetters(string $property, int $value): void
    {
        $entity = $this->createEntity();
        $setterMethod = 'set' . ucfirst($property);
        $getterMethod = 'get' . ucfirst($property);

        // @phpstan-ignore-next-line
        $entity->{$setterMethod}($value);

        // @phpstan-ignore-next-line
        $this->assertSame($value, $entity->{$getterMethod}(), 'Getter should return the value set by setter');
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{0: string, 1: int}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'callbackCount' => ['callbackCount', 100],
            'callbackSuccessCount' => ['callbackSuccessCount', 95],
            'callbackFailCount' => ['callbackFailCount', 5],
            'internalErrorCount' => ['internalErrorCount', 2],
            'invalidNumberCount' => ['invalidNumberCount', 3],
            'shutdownErrorCount' => ['shutdownErrorCount', 1],
            'blackListCount' => ['blackListCount', 0],
            'frequencyLimitCount' => ['frequencyLimitCount', 4],
        ];
    }

    public function testSettersWork(): void
    {
        $entity = $this->createEntity();
        $entity->setCallbackCount(100);
        $entity->setCallbackSuccessCount(95);
        $entity->setCallbackFailCount(5);
        $entity->setInternalErrorCount(2);
        $entity->setInvalidNumberCount(3);
        $entity->setShutdownErrorCount(1);
        $entity->setBlackListCount(0);
        $entity->setFrequencyLimitCount(4);

        // 验证值设置正确
        $this->assertSame(100, $entity->getCallbackCount());
        $this->assertSame(95, $entity->getCallbackSuccessCount());
        $this->assertSame(5, $entity->getCallbackFailCount());
    }

    public function testCanBeInstantiated(): void
    {
        $entity = $this->createEntity();
        $this->assertNotNull($entity);
    }

    public function testDefaultValues(): void
    {
        $entity = $this->createEntity();
        $this->assertEquals(0, $entity->getCallbackCount());
        $this->assertEquals(0, $entity->getCallbackSuccessCount());
        $this->assertEquals(0, $entity->getCallbackFailCount());
        $this->assertEquals(0, $entity->getInternalErrorCount());
        $this->assertEquals(0, $entity->getInvalidNumberCount());
        $this->assertEquals(0, $entity->getShutdownErrorCount());
        $this->assertEquals(0, $entity->getBlackListCount());
        $this->assertEquals(0, $entity->getFrequencyLimitCount());
    }
}
