<?php

namespace TencentCloudSmsBundle\Tests\Unit\Entity;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Entity\SmsMessage;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\MessageStatus;

class SmsMessageTest extends TestCase
{
    private SmsMessage $message;
    private Account $account;

    protected function setUp(): void
    {
        $this->message = new SmsMessage();
        $this->account = new Account();
        $this->account->setName('测试账号')
            ->setSecretId('test-secret-id')
            ->setSecretKey('test-secret-key');
    }

    public function testIdGetterSetter(): void
    {
        // ID通常由Doctrine生成，这里测试getter方法
        $this->assertEquals(0, $this->message->getId());
    }

    public function testAccountGetterSetter(): void
    {
        $this->message->setAccount($this->account);
        $this->assertSame($this->account, $this->message->getAccount());
    }

    public function testBatchIdGetterSetter(): void
    {
        // 测试构造函数生成的批次号
        $this->assertNotEmpty($this->message->getBatchId());
        
        // 测试手动设置的批次号
        $batchId = 'batch-123456';
        $this->message->setBatchId($batchId);
        $this->assertEquals($batchId, $this->message->getBatchId());
    }

    public function testSignatureGetterSetter(): void
    {
        $signature = '测试签名';
        $this->message->setSignature($signature);
        $this->assertEquals($signature, $this->message->getSignature());
    }

    public function testTemplateGetterSetter(): void
    {
        $template = 'SMS_12345678';
        $this->message->setTemplate($template);
        $this->assertEquals($template, $this->message->getTemplate());
    }

    public function testTemplateParamsGetterSetter(): void
    {
        // 默认应为空数组
        $this->assertEquals([], $this->message->getTemplateParams());
        
        // 设置参数
        $params = ['code' => '1234', 'product' => '测试产品'];
        $this->message->setTemplateParams($params);
        $this->assertEquals($params, $this->message->getTemplateParams());
    }

    public function testStatusGetterSetter(): void
    {
        // 默认状态应为SENDING
        $this->assertEquals(MessageStatus::SENDING, $this->message->getStatus());
        
        // 设置为SUCCESS
        $this->message->setStatus(MessageStatus::SUCCESS);
        $this->assertEquals(MessageStatus::SUCCESS, $this->message->getStatus());
        
        // 设置为FAILED
        $this->message->setStatus(MessageStatus::FAILED);
        $this->assertEquals(MessageStatus::FAILED, $this->message->getStatus());
    }

    public function testSendTimeGetterSetter(): void
    {
        // 默认应为null
        $this->assertNull($this->message->getSendTime());
        
        // 设置发送时间
        $now = new \DateTimeImmutable();
        $this->message->setSendTime($now);
        $this->assertSame($now, $this->message->getSendTime());
    }

    public function testRecipients(): void
    {
        // 默认应为空Collection
        $this->assertInstanceOf(Collection::class, $this->message->getRecipients());
        $this->assertEquals(0, $this->message->getRecipients()->count());
        
        // 添加接收人
        $recipient1 = new SmsRecipient();
        $recipient1->setPhoneNumber((new PhoneNumberInfo())->setPhoneNumber('13800138000'));
        
        $recipient2 = new SmsRecipient();
        $recipient2->setPhoneNumber((new PhoneNumberInfo())->setPhoneNumber('13900139000'));
        
        // 添加接收人并验证
        $this->message->addRecipient($recipient1);
        $this->assertEquals(1, $this->message->getRecipients()->count());
        $this->assertEquals($this->message, $recipient1->getMessage());
        
        $this->message->addRecipient($recipient2);
        $this->assertEquals(2, $this->message->getRecipients()->count());
        
        // 重复添加同一个接收人，数量不应增加
        $this->message->addRecipient($recipient1);
        $this->assertEquals(2, $this->message->getRecipients()->count());
        
        // 删除接收人并验证
        $this->message->removeRecipient($recipient1);
        $this->assertEquals(1, $this->message->getRecipients()->count());
        $this->assertNull($recipient1->getMessage());
    }

    public function testCreateTimeGetterSetter(): void
    {
        $now = new \DateTimeImmutable();
        $this->message->setCreateTime($now);
        $this->assertSame($now, $this->message->getCreateTime());
    }

    public function testUpdateTimeGetterSetter(): void
    {
        $now = new \DateTimeImmutable();
        $this->message->setUpdateTime($now);
        $this->assertSame($now, $this->message->getUpdateTime());
    }

    public function testFluidInterface(): void
    {
        // 测试流式接口（链式调用）
        $result = $this->message
            ->setAccount($this->account)
            ->setSignature('测试签名')
            ->setTemplate('SMS_12345678')
            ->setTemplateParams(['code' => '1234'])
            ->setStatus(MessageStatus::SUCCESS);
        
        // 验证返回值是否为当前对象实例
        $this->assertSame($this->message, $result);
    }
} 