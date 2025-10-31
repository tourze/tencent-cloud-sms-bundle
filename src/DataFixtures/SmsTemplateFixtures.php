<?php

namespace TencentCloudSmsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\Enum\TemplateReviewStatus;
use TencentCloudSmsBundle\Enum\TemplateType;

class SmsTemplateFixtures extends Fixture implements DependentFixtureInterface
{
    public const TEMPLATE_REFERENCE = 'template';
    public const TEMPLATE2_REFERENCE = 'template2';

    public function load(ObjectManager $manager): void
    {
        $account = $this->getReference(AccountFixtures::ACCOUNT_REFERENCE, Account::class);

        $template = new SmsTemplate();
        $template->setAccount($account);
        $template->setTemplateId('100001');
        $template->setTemplateName('验证码模板');
        $template->setTemplateContent('您的验证码是{1}');
        $template->setTemplateType(TemplateType::REGULAR);
        $template->setInternational(false);
        $template->setTemplateStatus(TemplateReviewStatus::APPROVED);
        $template->setReviewReply('审核通过');
        $template->setSyncing(true);

        $manager->persist($template);

        $template2 = new SmsTemplate();
        $template2->setAccount($account);
        $template2->setTemplateId('100002');
        $template2->setTemplateName('通知模板');
        $template2->setTemplateContent('您有新的订单{1}');
        $template2->setTemplateType(TemplateType::NOTIFICATION);
        $template2->setInternational(true);
        $template2->setTemplateStatus(TemplateReviewStatus::PENDING);
        $template2->setReviewReply('');
        $template2->setSyncing(true);

        $manager->persist($template2);

        $manager->flush();

        $this->addReference(self::TEMPLATE_REFERENCE, $template);
        $this->addReference(self::TEMPLATE2_REFERENCE, $template2);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
