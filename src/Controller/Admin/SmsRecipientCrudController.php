<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TencentCloudSmsBundle\Entity\SmsRecipient;
use TencentCloudSmsBundle\Enum\SendStatus;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

#[AdminCrud(routePath: '/tencent-sms/recipient', routeName: 'tencent_sms_recipient')]
final class SmsRecipientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SmsRecipient::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('短信接收者')
            ->setEntityLabelInPlural('短信接收者管理')
            ->setSearchFields(['serialNo', 'code'])
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('message')
            ->add('phoneNumber')
            ->add('serialNo')
            ->add('code')
            ->add('status')
            ->add('createTime')
            ->add('updateTime')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield AssociationField::new('message', '短信消息')
            ->setRequired(true)
            ->setHelp('关联的短信消息')
        ;

        yield AssociationField::new('phoneNumber', '手机号信息')
            ->setRequired(true)
            ->setHelp('接收短信的手机号')
        ;

        $statusField = EnumField::new('status', '发送状态');
        $statusField->setEnumCases(SendStatus::cases());
        $statusField->setHelp('短信发送状态');
        yield $statusField;

        yield TextField::new('serialNo', '序列号')
            ->setHelp('腾讯云返回的序列号')
        ;

        yield IntegerField::new('fee', '计费条数')
            ->setHelp('短信计费条数')
        ;

        yield TextField::new('code', '状态码')
            ->setHelp('腾讯云返回的状态码')
        ;

        yield TextareaField::new('statusMessage', '状态消息')
            ->hideOnIndex()
            ->setHelp('腾讯云返回的状态消息')
        ;

        yield DateTimeField::new('sendTime', '发送时间')
            ->setHelp('短信发送时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('receiveTime', '接收时间')
            ->setHelp('短信接收时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('statusTime', '状态更新时间')
            ->setHelp('状态最后更新时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield ArrayField::new('rawResponse', '原始响应')
            ->hideOnIndex()
            ->setHelp('腾讯云返回的原始响应数据')
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
