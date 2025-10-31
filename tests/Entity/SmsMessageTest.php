<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Entity\SmsMessage;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\MessageStatus;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(SmsMessage::class)]
final class SmsMessageTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): SmsMessage
    {
        $message = new SmsMessage();

        // 设置必需的属性以避免构造函数错误
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $message->setAccount($account);
        $message->setSignature('测试签名');
        $message->setTemplate('测试模板');

        return $message;
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'batchId' => ['batchId', 'batch-123456'],
            'signature' => ['signature', '测试签名'],
            'template' => ['template', 'SMS_12345678'],
            'templateParams' => ['templateParams', ['code' => '1234', 'product' => '测试产品']],
            'status' => ['status', MessageStatus::SUCCESS],
            'sendTime' => ['sendTime', new \DateTimeImmutable()],
            'createTime' => ['createTime', new \DateTimeImmutable()],
            'updateTime' => ['updateTime', new \DateTimeImmutable()],
        ];
    }

    public function testSettersWork(): void
    {
        // 测试setter方法功能正常
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $entity = $this->createEntity();
        $entity->setAccount($account);
        $entity->setSignature('测试签名');
        $entity->setTemplate('SMS_12345678');
        $entity->setTemplateParams(['code' => '1234']);
        $entity->setStatus(MessageStatus::SUCCESS);

        // 验证值设置正确
        $this->assertSame($account, $entity->getAccount());
        $this->assertSame('测试签名', $entity->getSignature());
        $this->assertSame('SMS_12345678', $entity->getTemplate());
        $this->assertSame(['code' => '1234'], $entity->getTemplateParams());
        $this->assertSame(MessageStatus::SUCCESS, $entity->getStatus());
    }

    public function testIdGetterSetter(): void
    {
        // ID通常由Doctrine生成，这里测试getter方法
        $entity = $this->createEntity();
        $this->assertEquals(0, $entity->getId());
    }

    public function testAccountGetterSetter(): void
    {
        $entity = $this->createEntity();
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $entity->setAccount($account);
        $this->assertSame($account, $entity->getAccount());
    }

    public function testBatchIdGetterSetter(): void
    {
        $entity = $this->createEntity();

        // 测试构造函数生成的批次号
        $this->assertNotEmpty($entity->getBatchId());

        // 测试手动设置的批次号
        $batchId = 'batch-123456';
        $entity->setBatchId($batchId);
        $this->assertEquals($batchId, $entity->getBatchId());
    }

    public function testSignatureGetterSetter(): void
    {
        $entity = $this->createEntity();
        $signature = '测试签名';
        $entity->setSignature($signature);
        $this->assertEquals($signature, $entity->getSignature());
    }

    public function testTemplateGetterSetter(): void
    {
        $entity = $this->createEntity();
        $template = 'SMS_12345678';
        $entity->setTemplate($template);
        $this->assertEquals($template, $entity->getTemplate());
    }

    public function testTemplateParamsGetterSetter(): void
    {
        $entity = $this->createEntity();

        // 默认应为空数组
        $this->assertEquals([], $entity->getTemplateParams());

        // 设置参数
        $params = ['code' => '1234', 'product' => '测试产品'];
        $entity->setTemplateParams($params);
        $this->assertEquals($params, $entity->getTemplateParams());
    }

    public function testStatusGetterSetter(): void
    {
        $entity = $this->createEntity();

        // 默认状态应为SENDING
        $this->assertEquals(MessageStatus::SENDING, $entity->getStatus());

        // 设置为SUCCESS
        $entity->setStatus(MessageStatus::SUCCESS);
        $this->assertEquals(MessageStatus::SUCCESS, $entity->getStatus());

        // 设置为FAILED
        $entity->setStatus(MessageStatus::FAILED);
        $this->assertEquals(MessageStatus::FAILED, $entity->getStatus());
    }

    public function testSendTimeGetterSetter(): void
    {
        $entity = $this->createEntity();

        // 默认应为null
        $this->assertNull($entity->getSendTime());

        // 设置发送时间
        $now = new \DateTimeImmutable();
        $entity->setSendTime($now);
        $this->assertSame($now, $entity->getSendTime());
    }

    public function testRecipients(): void
    {
        $entity = $this->createEntity();

        // 默认应为空Collection
        $this->assertEquals(0, $entity->getRecipients()->count());

        // 添加接收人
        $recipient1 = new SmsRecipient();
        $phoneNumberInfo1 = new PhoneNumberInfo();
        $phoneNumberInfo1->setPhoneNumber('13800138000');
        $recipient1->setPhoneNumber($phoneNumberInfo1);

        $recipient2 = new SmsRecipient();
        $phoneNumberInfo2 = new PhoneNumberInfo();
        $phoneNumberInfo2->setPhoneNumber('13900139000');
        $recipient2->setPhoneNumber($phoneNumberInfo2);

        // 添加接收人并验证
        $entity->addRecipient($recipient1);
        $this->assertEquals(1, $entity->getRecipients()->count());
        $this->assertEquals($entity, $recipient1->getMessage());

        $entity->addRecipient($recipient2);
        $this->assertEquals(2, $entity->getRecipients()->count());

        // 重复添加同一个接收人，数量不应增加
        $entity->addRecipient($recipient1);
        $this->assertEquals(2, $entity->getRecipients()->count());

        // 删除接收人并验证
        $entity->removeRecipient($recipient1);
        $this->assertEquals(1, $entity->getRecipients()->count());
        $this->assertNull($recipient1->getMessage());
    }

    public function testCreateTimeGetterSetter(): void
    {
        $entity = $this->createEntity();
        $now = new \DateTimeImmutable();
        $entity->setCreateTime($now);
        $this->assertSame($now, $entity->getCreateTime());
    }

    public function testUpdateTimeGetterSetter(): void
    {
        $entity = $this->createEntity();
        $now = new \DateTimeImmutable();
        $entity->setUpdateTime($now);
        $this->assertSame($now, $entity->getUpdateTime());
    }

    public function testCanBeInstantiated(): void
    {
        $entity = $this->createEntity();
        $this->assertNotNull($entity);
    }

    public function testImplementsStringable(): void
    {
        $entity = $this->createEntity();
        // Test that the string conversion works without error
        $stringValue = (string) $entity;
        $this->assertNotEmpty($stringValue);
    }
}
