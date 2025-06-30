<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\SmsStatistics;

class SmsStatisticsTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $statistics = new SmsStatistics();
        $this->assertInstanceOf(SmsStatistics::class, $statistics);
    }

    public function testImplementsStringable(): void
    {
        $statistics = new SmsStatistics();
        
        // 设置必需的属性以避免 __toString 方法出错
        $account = new \TencentCloudSmsBundle\Entity\Account();
        $account->setName('测试账号')
            ->setSecretId('test-secret-id')
            ->setSecretKey('test-secret-key');
        
        $statistics->setHour(new \DateTimeImmutable())
            ->setAccount($account);
        
        $this->assertInstanceOf(\Stringable::class, $statistics);
        // Test that the string conversion works without error
        $stringValue = (string) $statistics;
        $this->assertNotEmpty($stringValue);
    }
}
