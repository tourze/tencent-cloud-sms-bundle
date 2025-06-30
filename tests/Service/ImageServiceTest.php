<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Service\ImageService;

class ImageServiceTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $service = new ImageService();
        $this->assertInstanceOf(ImageService::class, $service);
    }
} 