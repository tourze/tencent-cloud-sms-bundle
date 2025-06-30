<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsTemplate;

class SmsTemplateTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $template = new SmsTemplate();
        $this->assertInstanceOf(SmsTemplate::class, $template);
    }

    public function testImplementsStringable(): void
    {
        $template = new SmsTemplate();
        $this->assertInstanceOf(\Stringable::class, $template);
        // Test that the string conversion works without error
        $stringValue = (string) $template;
        $this->assertNotEmpty($stringValue);
    }
}
