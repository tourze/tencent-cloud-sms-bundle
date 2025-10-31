<?php

namespace TencentCloudSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsStatistics;

class SmsStatisticsFixtures extends Fixture implements DependentFixtureInterface
{
    public const SMS_STATISTICS_REFERENCE = 'sms-statistics';
    public const SMS_STATISTICS2_REFERENCE = 'sms-statistics2';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $statistics = new SmsStatistics();
        $statistics->setAccount($account);
        $statistics->setHour(new \DateTimeImmutable('today 12:00:00'));

        $manager->persist($statistics);

        $statistics2 = new SmsStatistics();
        $statistics2->setAccount($account);
        $statistics2->setHour(new \DateTimeImmutable('yesterday 14:00:00'));

        $manager->persist($statistics2);

        $manager->flush();

        $this->addReference(self::SMS_STATISTICS_REFERENCE, $statistics);
        $this->addReference(self::SMS_STATISTICS2_REFERENCE, $statistics2);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
