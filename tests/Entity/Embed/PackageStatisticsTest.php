<?php

namespace TencentCloudSmsBundle\Tests\Entity\Embed;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\Embed\PackageStatistics;

class PackageStatisticsTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $statistics = new PackageStatistics();
        $this->assertInstanceOf(PackageStatistics::class, $statistics);
    }
}
