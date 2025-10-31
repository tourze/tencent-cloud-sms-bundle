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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;

#[AdminCrud(routePath: '/tencent-sms/phone', routeName: 'tencent_sms_phone')]
final class PhoneNumberInfoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PhoneNumberInfo::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('手机号信息')
            ->setEntityLabelInPlural('手机号信息管理')
            ->setSearchFields(['phoneNumber', 'nationCode', 'isoCode', 'isoName'])
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('phoneNumber')
            ->add('nationCode')
            ->add('isoCode')
            ->add('isoName')
            ->add('syncing')
            ->add('createTime')
            ->add('updateTime')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield TextField::new('phoneNumber', '手机号码')
            ->setRequired(true)
            ->setHelp('完整的手机号码，包含国际区号')
        ;

        yield TextField::new('nationCode', '国家码')
            ->setHelp('手机号的国家/地区代码')
        ;

        yield TextField::new('isoCode', 'ISO编码')
            ->setHelp('国家/地区的ISO编码')
        ;

        yield TextField::new('isoName', '国家/地区名称')
            ->setHelp('国家或地区的名称')
        ;

        yield TextField::new('subscriberNumber', '用户号码')
            ->setHelp('去除国家码后的用户号码')
        ;

        yield TextField::new('fullNumber', '完整号码')
            ->setHelp('标准化后的完整手机号码')
        ;

        yield TextField::new('code', '状态码')
            ->setHelp('查询操作的状态码')
        ;

        yield TextareaField::new('message', '查询结果')
            ->hideOnIndex()
            ->setHelp('手机号码查询的详细结果信息')
        ;

        yield BooleanField::new('syncing', '正在同步')
            ->setHelp('标记是否正在进行同步操作')
        ;

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
