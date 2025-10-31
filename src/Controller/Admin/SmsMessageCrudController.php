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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TencentCloudSmsBundle\Entity\SmsMessage;
use TencentCloudSmsBundle\Enum\MessageStatus;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

#[AdminCrud(routePath: '/tencent-sms/message', routeName: 'tencent_sms_message')]
final class SmsMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SmsMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('短信消息')
            ->setEntityLabelInPlural('短信消息管理')
            ->setSearchFields(['batchId', 'signature', 'template'])
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('account')
            ->add('batchId')
            ->add('signature')
            ->add('template')
            ->add('status')
            ->add('createTime')
            ->add('updateTime')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield AssociationField::new('account', '腾讯云账号')
            ->setRequired(true)
            ->setHelp('选择使用的腾讯云账号')
        ;

        yield TextField::new('batchId', '批次号')
            ->setRequired(true)
            ->setHelp('短信发送的批次标识符')
        ;

        yield TextField::new('signature', '短信签名')
            ->setRequired(true)
            ->setHelp('短信签名内容')
        ;

        yield TextField::new('template', '短信模板ID')
            ->setRequired(true)
            ->setHelp('短信模板的唯一标识符')
        ;

        yield ArrayField::new('templateParams', '模板参数')
            ->setHelp('短信模板的参数数组')
            ->hideOnIndex()
            ->setRequired(false)
        ;

        $statusField = EnumField::new('status', '发送状态');
        $statusField->setEnumCases(MessageStatus::cases());
        $statusField->setHelp('当前短信的发送状态');
        yield $statusField;

        yield DateTimeField::new('sendTime', '发送时间')
            ->setHelp('短信发送的时间')
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield AssociationField::new('recipients', '接收者列表')
            ->onlyOnIndex()
            ->setHelp('此短信的所有接收者')
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
