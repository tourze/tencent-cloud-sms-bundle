<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * AdminMenu服务测试
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // Setup for AdminMenu tests
    }

    public function testInvokeAddsMenuItems(): void
    {
        $container = self::getContainer();
        /** @var AdminMenu $adminMenu */
        $adminMenu = $container->get(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证菜单结构
        $smsMenu = $rootItem->getChild('腾讯云短信');
        self::assertNotNull($smsMenu);

        $accountMenu = $smsMenu->getChild('账号管理');
        self::assertNotNull($accountMenu);

        $signatureMenu = $smsMenu->getChild('签名管理');
        self::assertNotNull($signatureMenu);

        $templateMenu = $smsMenu->getChild('模板管理');
        self::assertNotNull($templateMenu);

        $messageMenu = $smsMenu->getChild('消息管理');
        self::assertNotNull($messageMenu);

        $recipientMenu = $smsMenu->getChild('接收者管理');
        self::assertNotNull($recipientMenu);

        $phoneMenu = $smsMenu->getChild('手机号管理');
        self::assertNotNull($phoneMenu);

        $statisticsMenu = $smsMenu->getChild('统计数据');
        self::assertNotNull($statisticsMenu);
    }

    public function testCanBeInstantiated(): void
    {
        $container = self::getContainer();
        $adminMenu = $container->get(AdminMenu::class);

        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }
}
