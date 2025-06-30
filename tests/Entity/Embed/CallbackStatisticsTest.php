<?php

namespace TencentCloudSmsBundle\Tests\Entity\Embed;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\Embed\CallbackStatistics;

class CallbackStatisticsTest extends TestCase
{
    private CallbackStatistics $statistics;

    public function testDefaultValues(): void
    {
        $this->assertEquals(0, $this->statistics->getCallbackCount());
        $this->assertEquals(0, $this->statistics->getCallbackSuccessCount());
        $this->assertEquals(0, $this->statistics->getCallbackFailCount());
        $this->assertEquals(0, $this->statistics->getInternalErrorCount());
        $this->assertEquals(0, $this->statistics->getInvalidNumberCount());
        $this->assertEquals(0, $this->statistics->getShutdownErrorCount());
        $this->assertEquals(0, $this->statistics->getBlackListCount());
        $this->assertEquals(0, $this->statistics->getFrequencyLimitCount());
    }

    public function testCallbackCountGetterSetter(): void
    {
        $value = 100;
        $result = $this->statistics->setCallbackCount($value);

        $this->assertSame($this->statistics, $result);
        $this->assertEquals($value, $this->statistics->getCallbackCount());
    }

    public function testCallbackSuccessCountGetterSetter(): void
    {
        $value = 95;
        $result = $this->statistics->setCallbackSuccessCount($value);

        $this->assertSame($this->statistics, $result);
        $this->assertEquals($value, $this->statistics->getCallbackSuccessCount());
    }

    public function testCallbackFailCountGetterSetter(): void
    {
        $value = 5;
        $result = $this->statistics->setCallbackFailCount($value);

        $this->assertSame($this->statistics, $result);
        $this->assertEquals($value, $this->statistics->getCallbackFailCount());
    }

    public function testInternalErrorCountGetterSetter(): void
    {
        $value = 2;
        $result = $this->statistics->setInternalErrorCount($value);

        $this->assertSame($this->statistics, $result);
        $this->assertEquals($value, $this->statistics->getInternalErrorCount());
    }

    public function testInvalidNumberCountGetterSetter(): void
    {
        $value = 3;
        $result = $this->statistics->setInvalidNumberCount($value);

        $this->assertSame($this->statistics, $result);
        $this->assertEquals($value, $this->statistics->getInvalidNumberCount());
    }

    public function testShutdownErrorCountGetterSetter(): void
    {
        $value = 1;
        $result = $this->statistics->setShutdownErrorCount($value);

        $this->assertSame($this->statistics, $result);
        $this->assertEquals($value, $this->statistics->getShutdownErrorCount());
    }

    public function testBlackListCountGetterSetter(): void
    {
        $value = 0;
        $result = $this->statistics->setBlackListCount($value);

        $this->assertSame($this->statistics, $result);
        $this->assertEquals($value, $this->statistics->getBlackListCount());
    }

    public function testFrequencyLimitCountGetterSetter(): void
    {
        $value = 4;
        $result = $this->statistics->setFrequencyLimitCount($value);

        $this->assertSame($this->statistics, $result);
        $this->assertEquals($value, $this->statistics->getFrequencyLimitCount());
    }

    public function testChainedSetters(): void
    {
        $result = $this->statistics
            ->setCallbackCount(100)
            ->setCallbackSuccessCount(95)
            ->setCallbackFailCount(5)
            ->setInternalErrorCount(2)
            ->setInvalidNumberCount(3)
            ->setShutdownErrorCount(1)
            ->setBlackListCount(0)
            ->setFrequencyLimitCount(4);

        $this->assertSame($this->statistics, $result);
        $this->assertEquals(100, $this->statistics->getCallbackCount());
        $this->assertEquals(95, $this->statistics->getCallbackSuccessCount());
        $this->assertEquals(5, $this->statistics->getCallbackFailCount());
        $this->assertEquals(2, $this->statistics->getInternalErrorCount());
        $this->assertEquals(3, $this->statistics->getInvalidNumberCount());
        $this->assertEquals(1, $this->statistics->getShutdownErrorCount());
        $this->assertEquals(0, $this->statistics->getBlackListCount());
        $this->assertEquals(4, $this->statistics->getFrequencyLimitCount());
    }

    protected function setUp(): void
    {
        $this->statistics = new CallbackStatistics();
    }
}
