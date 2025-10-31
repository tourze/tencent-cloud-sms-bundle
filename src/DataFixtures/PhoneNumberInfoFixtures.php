<?php

namespace TencentCloudSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;

class PhoneNumberInfoFixtures extends Fixture
{
    public const PHONE_INFO_REFERENCE = 'phone-info';
    public const PHONE_INFO2_REFERENCE = 'phone-info2';

    public function load(ObjectManager $manager): void
    {
        $phoneInfo = new PhoneNumberInfo();
        $phoneInfo->setPhoneNumber('+8613800138000');
        $phoneInfo->setNationCode('86');
        $phoneInfo->setIsoCode('CN');
        $phoneInfo->setIsoName('China');
        $phoneInfo->setSubscriberNumber('13800138000');
        $phoneInfo->setFullNumber('8613800138000');
        $phoneInfo->setMessage('');
        $phoneInfo->setCode('0');
        $phoneInfo->setSyncing(false);

        $manager->persist($phoneInfo);

        $phoneInfo2 = new PhoneNumberInfo();
        $phoneInfo2->setPhoneNumber('+1234567890');
        $phoneInfo2->setSyncing(true);

        $manager->persist($phoneInfo2);

        $manager->flush();

        $this->addReference(self::PHONE_INFO_REFERENCE, $phoneInfo);
        $this->addReference(self::PHONE_INFO2_REFERENCE, $phoneInfo2);
    }
}
