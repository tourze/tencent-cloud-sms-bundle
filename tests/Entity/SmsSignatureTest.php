<?php

namespace TencentCloudSmsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Enum\DocumentType;
use TencentCloudSmsBundle\Enum\SignPurpose;
use TencentCloudSmsBundle\Enum\SignReviewStatus;
use TencentCloudSmsBundle\Enum\SignType;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(SmsSignature::class)]
final class SmsSignatureTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): SmsSignature
    {
        $signature = new SmsSignature();

        // 设置必需的属性以避免构造函数错误
        $account = new Account();
        $account->setName('测试账号');
        $account->setSecretId('test-secret-id');
        $account->setSecretKey('test-secret-key');

        $signature->setAccount($account);
        $signature->setSignName('测试签名');

        return $signature;
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{0: string, 1: mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'signId' => ['signId', 'sign-123456'],
            'signName' => ['signName', '测试签名'],
            'signType' => ['signType', SignType::WEBSITE],
            'documentType' => ['documentType', DocumentType::BUSINESS_LICENSE],
            'signPurpose' => ['signPurpose', SignPurpose::SELF_USE],
            'international' => ['international', false],
            'signStatus' => ['signStatus', SignReviewStatus::PENDING],
            'reviewReply' => ['reviewReply', '审核通过'],
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
        $entity->setSignId('sign-123456');
        $entity->setSignName('测试签名');
        $entity->setSignType(SignType::WEBSITE);
        $entity->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $entity->setSignPurpose(SignPurpose::SELF_USE);
        $entity->setInternational(false);
        $entity->setSignStatus(SignReviewStatus::PENDING);
        $entity->setReviewReply('审核通过');

        // 验证值设置正确
        $this->assertSame('sign-123456', $entity->getSignId());
        $this->assertSame('测试签名', $entity->getSignName());
        $this->assertSame(SignType::WEBSITE, $entity->getSignType());
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

    public function testSignIdGetterSetter(): void
    {
        $entity = $this->createEntity();
        $signId = 'sign-123456';
        $entity->setSignId($signId);
        $this->assertEquals($signId, $entity->getSignId());
    }

    public function testSignNameGetterSetter(): void
    {
        $entity = $this->createEntity();
        $signName = '测试签名';
        $entity->setSignName($signName);
        $this->assertEquals($signName, $entity->getSignName());
    }

    public function testSignTypeGetterSetter(): void
    {
        $entity = $this->createEntity();
        $signType = SignType::WEBSITE;
        $entity->setSignType($signType);
        $this->assertEquals($signType, $entity->getSignType());
    }

    public function testDocumentTypeGetterSetter(): void
    {
        $entity = $this->createEntity();
        $documentType = DocumentType::BUSINESS_LICENSE;
        $entity->setDocumentType($documentType);
        $this->assertEquals($documentType, $entity->getDocumentType());
    }

    public function testSignPurposeGetterSetter(): void
    {
        $entity = $this->createEntity();
        $signPurpose = SignPurpose::SELF_USE;
        $entity->setSignPurpose($signPurpose);
        $this->assertEquals($signPurpose, $entity->getSignPurpose());
    }

    public function testInternationalGetterSetter(): void
    {
        $entity = $this->createEntity();
        $international = false;
        $entity->setInternational($international);
        $this->assertEquals($international, $entity->isInternational());
    }

    public function testSignStatusGetterSetter(): void
    {
        $entity = $this->createEntity();
        $signStatus = SignReviewStatus::PENDING;
        $entity->setSignStatus($signStatus);
        $this->assertEquals($signStatus, $entity->getSignStatus());
    }

    public function testReviewReplyGetterSetter(): void
    {
        $entity = $this->createEntity();
        $reviewReply = '审核通过';
        $entity->setReviewReply($reviewReply);
        $this->assertEquals($reviewReply, $entity->getReviewReply());
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
}
