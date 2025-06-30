<?php

namespace TencentCloudSmsBundle\Tests\Entity\Embed;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\Embed\SendStatistics;

class SendStatisticsTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $statistics = new SendStatistics();
        $this->assertInstanceOf(SendStatistics::class, $statistics);
    }
}
