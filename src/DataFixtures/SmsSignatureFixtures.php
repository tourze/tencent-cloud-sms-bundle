<?php

namespace TencentCloudSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Enum\DocumentType;
use TencentCloudSmsBundle\Enum\SignPurpose;
use TencentCloudSmsBundle\Enum\SignReviewStatus;
use TencentCloudSmsBundle\Enum\SignType;

class SmsSignatureFixtures extends Fixture implements DependentFixtureInterface
{
    public const SIGNATURE_REFERENCE = 'signature';
    public const SIGNATURE2_REFERENCE = 'signature2';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $signature = new SmsSignature();
        $signature->setAccount($account);
        $signature->setSignId('100001');
        $signature->setSignName('测试签名');
        $signature->setSignType(SignType::APP);
        $signature->setSignPurpose(SignPurpose::SELF_USE);
        $signature->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature->setDocumentUrl('https://test.example/document.jpg');
        $signature->setInternational(false);
        $signature->setSignStatus(SignReviewStatus::APPROVED);
        $signature->setReviewReply('审核通过');
        $signature->setSyncing(true);

        $manager->persist($signature);

        $signature2 = new SmsSignature();
        $signature2->setAccount($account);
        $signature2->setSignId('100002');
        $signature2->setSignName('测试签名2');
        $signature2->setSignType(SignType::WEBSITE);
        $signature2->setSignPurpose(SignPurpose::OTHER_USE);
        $signature2->setDocumentType(DocumentType::BUSINESS_LICENSE);
        $signature2->setDocumentUrl('https://test.example/document.jpg');
        $signature2->setInternational(true);
        $signature2->setSignStatus(SignReviewStatus::PENDING);
        $signature2->setReviewReply('');
        $signature2->setSyncing(true);

        $manager->persist($signature2);

        $manager->flush();

        $this->addReference(self::SIGNATURE_REFERENCE, $signature);
        $this->addReference(self::SIGNATURE2_REFERENCE, $signature2);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
