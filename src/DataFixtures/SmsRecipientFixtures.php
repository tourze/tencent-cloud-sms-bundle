<?php

namespace TencentCloudSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Entity\SmsMessage;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\SendStatus;

class SmsRecipientFixtures extends Fixture implements DependentFixtureInterface
{
    public const SMS_RECIPIENT_REFERENCE = 'sms-recipient';
    public const SMS_RECIPIENT2_REFERENCE = 'sms-recipient2';

    public function load(ObjectManager $manager): void
    {
        $message = $this->getReference(SmsMessageFixtures::SMS_MESSAGE_REFERENCE, SmsMessage::class);

        $phoneInfo = $this->getReference(PhoneNumberInfoFixtures::PHONE_INFO_REFERENCE, PhoneNumberInfo::class);
        $phoneInfo2 = $this->getReference(PhoneNumberInfoFixtures::PHONE_INFO2_REFERENCE, PhoneNumberInfo::class);

        $recipient = new SmsRecipient();
        $recipient->setMessage($message);
        $recipient->setPhoneNumber($phoneInfo);
        $recipient->setSerialNo('serial123');
        $recipient->setStatus(SendStatus::SUCCESS);
        $recipient->setStatusMessage('Success');
        $recipient->setFee(1);

        $manager->persist($recipient);

        $recipient2 = new SmsRecipient();
        $recipient2->setMessage($message);
        $recipient2->setPhoneNumber($phoneInfo2);
        $recipient2->setSerialNo('serial456');
        $recipient2->setStatus(SendStatus::FAIL);
        $recipient2->setStatusMessage('');
        $recipient2->setFee(0);

        $manager->persist($recipient2);

        $manager->flush();

        $this->addReference(self::SMS_RECIPIENT_REFERENCE, $recipient);
        $this->addReference(self::SMS_RECIPIENT2_REFERENCE, $recipient2);
    }

    public function getDependencies(): array
    {
        return [
            SmsMessageFixtures::class,
            PhoneNumberInfoFixtures::class,
        ];
    }
}
