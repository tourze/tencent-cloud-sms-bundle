<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TencentCloudSmsBundle\Entity\Account;

#[AdminCrud(routePath: '/tencent-sms/account', routeName: 'tencent_sms_account')]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('腾讯云账号')
            ->setEntityLabelInPlural('腾讯云账号管理')
            ->setSearchFields(['name', 'secretId'])
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('secretId')
            ->add('valid')
            ->add('createTime')
            ->add('updateTime')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield TextField::new('name', '名称')
            ->setRequired(true)
            ->setHelp('账号的显示名称，便于识别')
        ;

        yield TextField::new('secretId', 'SecretId')
            ->setRequired(true)
            ->setHelp('腾讯云 API 密钥 ID')
        ;

        yield TextField::new('secretKey', 'SecretKey')
            ->setRequired(true)
            ->hideOnIndex()
            ->setHelp('腾讯云 API 密钥')
        ;

        yield BooleanField::new('valid', '是否有效')
            ->setHelp('标记此账号是否可用')
        ;

        yield TextField::new('createdBy', '创建者')->onlyOnIndex();
        yield TextField::new('updatedBy', '更新者')->onlyOnIndex();

        yield DateTimeField::new('createTime', '创建时间')
            ->onlyOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }
}
