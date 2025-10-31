<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Entity\SmsMessage;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Entity\SmsStatistics;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 腾讯云短信服务管理后台菜单提供者
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        // 创建腾讯云短信服务主菜单
        if (null === $item->getChild('腾讯云短信')) {
            $item->addChild('腾讯云短信')
                ->setAttribute('icon', 'fas fa-sms')
            ;
        }

        $smsMenu = $item->getChild('腾讯云短信');
        if (null === $smsMenu) {
            return;
        }

        // 账号管理
        $smsMenu->addChild('账号管理')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-key')
        ;

        // 签名管理
        $smsMenu->addChild('签名管理')
            ->setUri($this->linkGenerator->getCurdListPage(SmsSignature::class))
            ->setAttribute('icon', 'fas fa-signature')
        ;

        // 模板管理
        $smsMenu->addChild('模板管理')
            ->setUri($this->linkGenerator->getCurdListPage(SmsTemplate::class))
            ->setAttribute('icon', 'fas fa-file-alt')
        ;

        // 消息管理
        $smsMenu->addChild('消息管理')
            ->setUri($this->linkGenerator->getCurdListPage(SmsMessage::class))
            ->setAttribute('icon', 'fas fa-envelope')
        ;

        // 接收者管理
        $smsMenu->addChild('接收者管理')
            ->setUri($this->linkGenerator->getCurdListPage(SmsRecipient::class))
            ->setAttribute('icon', 'fas fa-users')
        ;

        // 手机号管理
        $smsMenu->addChild('手机号管理')
            ->setUri($this->linkGenerator->getCurdListPage(PhoneNumberInfo::class))
            ->setAttribute('icon', 'fas fa-mobile-alt')
        ;

        // 统计数据
        $smsMenu->addChild('统计数据')
            ->setUri($this->linkGenerator->getCurdListPage(SmsStatistics::class))
            ->setAttribute('icon', 'fas fa-chart-bar')
        ;
    }
}
