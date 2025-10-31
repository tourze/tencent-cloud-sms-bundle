<?php

namespace TencentCloudSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use TencentCloudSmsBundle\Entity\Account;

class AccountFixtures extends Fixture
{
    public const ACCOUNT_REFERENCE = 'account';
    public const ACCOUNT2_REFERENCE = 'account2';

    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setSecretId('test_secret_id');
        $account->setSecretKey('test_secret_key');
        $account->setValid(true);

        $manager->persist($account);

        $account2 = new Account();
        $account2->setName('Test Account 2');
        $account2->setSecretId('test_secret_id_2');
        $account2->setSecretKey('test_secret_key_2');
        $account2->setValid(false);

        $manager->persist($account2);

        $manager->flush();

        $this->addReference(self::ACCOUNT_REFERENCE, $account);
        $this->addReference(self::ACCOUNT2_REFERENCE, $account2);
    }
}
