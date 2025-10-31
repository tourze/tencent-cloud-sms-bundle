<?php

namespace TencentCloudSmsBundle\Tests\EventSubscriber;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Enum\DocumentType;
use TencentCloudSmsBundle\Enum\SignPurpose;
use TencentCloudSmsBundle\Enum\SignReviewStatus;
use TencentCloudSmsBundle\Enum\SignType;
use TencentCloudSmsBundle\EventSubscriber\SmsSignatureListener;
use TencentCloudSmsBundle\Exception\SignatureException;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(SmsSignatureListener::class)]
#[RunTestsInSeparateProcesses]
final class SmsSignatureListenerTest extends AbstractEventSubscriberTestCase
{
    private SmsSignatureListener $listener;

    protected function onSetUp(): void
    {
        // 通过容器获取服务
        $this->listener = self::getService(SmsSignatureListener::class);
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertNotNull($this->listener);
    }

    public function testCreateRemoteSignatureSkipsWhenSyncing(): void
    {
        // 创建一个处于同步状态的签名
        $signature = new SmsSignature();
        $signature->setSignName('Test Signature');
        $signature->setSyncing(true);

        // 由于设置了 syncing=true，监听器应该跳过远程调用
        $this->listener->createRemoteSignature($signature);

        // 验证签名没有被修改
        $this->assertNull($signature->getSignId());
        $this->assertTrue($signature->isSyncing());
    }

    public function testUpdateRemoteSignatureSkipsWhenSyncing(): void
    {
        // 创建一个处于同步状态的签名
        $signature = new SmsSignature();
        $signature->setSignName('Test Signature');
        $signature->setSignId('test-sign-id');
        $signature->setSyncing(true);

        // 由于设置了 syncing=true，监听器应该跳过远程调用
        $this->listener->updateRemoteSignature($signature);

        // 验证签名没有被修改
        $this->assertEquals('test-sign-id', $signature->getSignId());
        $this->assertTrue($signature->isSyncing());
    }

    public function testDeleteRemoteSignatureSkipsWhenSyncing(): void
    {
        // 创建一个处于同步状态的签名
        $signature = new SmsSignature();
        $signature->setSignName('Test Signature');
        $signature->setSignId('test-sign-id');
        $signature->setSyncing(true);

        // 由于设置了 syncing=true，监听器应该跳过远程调用
        $this->listener->deleteRemoteSignature($signature);

        // 验证签名没有被修改
        $this->assertEquals('test-sign-id', $signature->getSignId());
        $this->assertTrue($signature->isSyncing());
    }

    public function testCreateRemoteSignatureWithMissingRequiredFields(): void
    {
        // 创建一个缺少必需字段的签名
        $signature = new SmsSignature();
        $signature->setSignName('Test Signature');
        $signature->setSyncing(false); // 确保不是同步状态

        // 由于缺少必需字段，应该抛出异常
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('签名账号不能为空');

        $this->listener->createRemoteSignature($signature);
    }

    public function testCreateRemoteSignatureWithMissingSignType(): void
    {
        // 创建一个账号，但缺少签名类型
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        $signature = new SmsSignature();
        $signature->setSignName('Test Signature');
        $signature->setAccount($account);
        $signature->setSyncing(false); // 确保不是同步状态

        // 由于缺少签名类型，应该抛出异常
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('签名类型不能为空');

        $this->listener->createRemoteSignature($signature);
    }

    public function testCreateRemoteSignatureWithMissingDocumentType(): void
    {
        // 创建一个账号和签名类型，但缺少证明类型
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        $signature = new SmsSignature();
        $signature->setSignName('Test Signature');
        $signature->setAccount($account);
        $signature->setSignType(SignType::COMPANY);
        $signature->setSyncing(false); // 确保不是同步状态

        // 由于缺少证明类型，应该抛出异常
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('证明类型不能为空');

        $this->listener->createRemoteSignature($signature);
    }

    public function testCreateRemoteSignatureWithValidData(): void
    {
        // 创建一个完整的测试账号
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        // 创建一个完整的签名
        $signature = new SmsSignature();
        $signature->setSignName('Test Signature');
        $signature->setAccount($account);
        $signature->setSignType(SignType::COMPANY);
        $signature->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature->setDocumentUrl('https://example.com/test.jpg');
        $signature->setSignPurpose(SignPurpose::SELF_USE);
        $signature->setSignStatus(SignReviewStatus::REVIEWING);
        $signature->setSyncing(false);

        // 由于使用了无效的图片URL，ImageService会抛出SignatureException
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('无法读取图片文件：https://example.com/test.jpg');

        $this->listener->createRemoteSignature($signature);
    }

    public function testCreateRemoteSignatureValidationOrder(): void
    {
        // 创建一个只包含账号的签名
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');
        $account->setValid(true);

        $signature = new SmsSignature();
        $signature->setSignName('Test Signature');
        $signature->setAccount($account);
        $signature->setSyncing(false);

        // 应该首先检查签名类型
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('签名类型不能为空');

        $this->listener->createRemoteSignature($signature);
    }

    public function testSyncingStatePreservation(): void
    {
        // 测试同步状态在各种操作后都得到保持
        $signature = new SmsSignature();
        $signature->setSignName('Test Signature');
        $signature->setSyncing(true);

        // 执行所有操作
        $this->listener->createRemoteSignature($signature);
        $this->listener->updateRemoteSignature($signature);
        $this->listener->deleteRemoteSignature($signature);

        // 验证同步状态保持不变
        $this->assertTrue($signature->isSyncing());
        $this->assertNull($signature->getSignId());
    }
}
