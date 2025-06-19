<?php

namespace TencentCloudSmsBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\Account;

class AccountTest extends TestCase
{
    private Account $account;

    protected function setUp(): void
    {
        $this->account = new Account();
    }

    public function testIdGetterSetter(): void
    {
        // ID通常由Doctrine生成，这里测试getter方法
        $this->assertEquals(0, $this->account->getId());
    }

    public function testNameGetterSetter(): void
    {
        $name = '测试账号名称';
        $this->account->setName($name);
        $this->assertEquals($name, $this->account->getName());
    }

    public function testSecretIdGetterSetter(): void
    {
        $secretId = 'test-secret-id-12345';
        $this->account->setSecretId($secretId);
        $this->assertEquals($secretId, $this->account->getSecretId());
    }

    public function testSecretKeyGetterSetter(): void
    {
        $secretKey = 'test-secret-key-abcdef';
        $this->account->setSecretKey($secretKey);
        $this->assertEquals($secretKey, $this->account->getSecretKey());
    }

    public function testValidGetterSetter(): void
    {
        // 默认值应为false
        $this->assertFalse($this->account->isValid());
        
        // 设置为true
        $this->account->setValid(true);
        $this->assertTrue($this->account->isValid());
        
        // 设置为false
        $this->account->setValid(false);
        $this->assertFalse($this->account->isValid());
        
        // 设置为null
        $this->account->setValid(null);
        $this->assertNull($this->account->isValid());
    }

    public function testCreatedByGetterSetter(): void
    {
        $createdBy = 'test-user';
        $this->account->setCreatedBy($createdBy);
        $this->assertEquals($createdBy, $this->account->getCreatedBy());
    }

    public function testUpdatedByGetterSetter(): void
    {
        $updatedBy = 'another-user';
        $this->account->setUpdatedBy($updatedBy);
        $this->assertEquals($updatedBy, $this->account->getUpdatedBy());
    }

    public function testCreateTimeGetterSetter(): void
    {
        $now = new \DateTimeImmutable();
        $this->account->setCreateTime($now);
        $this->assertSame($now, $this->account->getCreateTime());
    }

    public function testUpdateTimeGetterSetter(): void
    {
        $now = new \DateTimeImmutable();
        $this->account->setUpdateTime($now);
        $this->assertSame($now, $this->account->getUpdateTime());
    }

    public function testFluidInterface(): void
    {
        // 测试流式接口（链式调用）
        $result = $this->account
            ->setName('测试账号')
            ->setSecretId('test-id')
            ->setSecretKey('test-key')
            ->setValid(true);
        
        // 验证返回值是否为当前对象实例
        $this->assertSame($this->account, $result);
    }
} 