<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use TencentCloudSmsBundle\Controller\Admin\AccountCrudController;

/**
 * 测试环境专用Dashboard控制器
 * 用于解决测试中AdminUrlGenerator需要Dashboard控制器的问题
 */
#[AdminDashboard(routePath: '/tencent-sms-test-admin', routeName: 'tencent_sms_test_admin')]
final class TestDashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(AccountCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('腾讯云短信测试后台')
        ;
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets();
    }

    public function configureMenuItems(): iterable
    {
        return parent::configureMenuItems();
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user);
    }

    public function configureActions(): Actions
    {
        return parent::configureActions();
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud();
    }

    public function configureFilters(): Filters
    {
        return parent::configureFilters();
    }
}
