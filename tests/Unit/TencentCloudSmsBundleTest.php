<?php

namespace TencentCloudSmsBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TencentCloudSmsBundle\TencentCloudSmsBundle;

class TencentCloudSmsBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new TencentCloudSmsBundle();
        $this->assertInstanceOf(TencentCloudSmsBundle::class, $bundle);
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function testGetName(): void
    {
        $bundle = new TencentCloudSmsBundle();
        $this->assertSame('TencentCloudSmsBundle', $bundle->getName());
    }
}
