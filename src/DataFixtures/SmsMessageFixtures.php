<?php

namespace TencentCloudSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsMessage;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\Enum\MessageStatus;

class SmsMessageFixtures extends Fixture implements DependentFixtureInterface
{
    public const SMS_MESSAGE_REFERENCE = 'sms-message';
    public const SMS_MESSAGE2_REFERENCE = 'sms-message2';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);
        $signature = $this->getReference(SmsSignatureFixtures::SIGNATURE_REFERENCE, SmsSignature::class);
        $template = $this->getReference(SmsTemplateFixtures::TEMPLATE_REFERENCE, SmsTemplate::class);

        $message = new SmsMessage();
        $message->setAccount($account);
        $message->setBatchId('batch123');
        $message->setSignature($signature);
        $message->setTemplate($template);
        $message->setTemplateParams(['code' => '123456']);
        $message->setStatus(MessageStatus::SUCCESS);
        $message->setSendTime(new \DateTimeImmutable());

        $manager->persist($message);

        $message2 = new SmsMessage();
        $message2->setAccount($account);
        $message2->setBatchId('batch456');
        $message2->setSignature($signature);
        $message2->setTemplate($template);
        $message2->setTemplateParams(['code' => '654321']);
        $message2->setStatus(MessageStatus::SENDING);
        $message2->setSendTime(new \DateTimeImmutable('+1 hour'));

        $manager->persist($message2);

        $manager->flush();

        $this->addReference(self::SMS_MESSAGE_REFERENCE, $message);
        $this->addReference(self::SMS_MESSAGE2_REFERENCE, $message2);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
            SmsSignatureFixtures::class,
            SmsTemplateFixtures::class,
        ];
    }
}
