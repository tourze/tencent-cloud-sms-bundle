<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use TencentCloudSmsBundle\Entity\SmsStatistics;

#[AdminCrud(routePath: '/tencent-sms/statistics', routeName: 'tencent_sms_statistics')]
final class SmsStatisticsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SmsStatistics::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('短信统计')
            ->setEntityLabelInPlural('短信统计管理')
            ->setSearchFields(['account.name'])
            ->setDefaultSort(['hour' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('account')
            ->add('hour')
            ->add('createTime')
            ->add('updateTime')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->onlyOnIndex();

        yield AssociationField::new('account', '腾讯云账号')
            ->setRequired(true)
            ->setHelp('关联的腾讯云账号')
        ;

        yield DateTimeField::new('hour', '统计小时')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:00')
            ->setHelp('统计数据对应的小时时间')
        ;

        // 发送统计字段
        yield IntegerField::new('sendStatistics.requestCount', '短信请求量')
            ->setHelp('统计小时内的短信请求总数')
            ->hideOnForm()
        ;

        yield IntegerField::new('sendStatistics.requestSuccessCount', '短信成功量')
            ->setHelp('统计小时内成功发送的短信数量')
            ->hideOnForm()
        ;

        yield IntegerField::new('sendStatistics.requestFailCount', '短信失败量')
            ->setHelp('统计小时内发送失败的短信数量')
            ->hideOnForm()
        ;

        // 回执统计字段
        yield IntegerField::new('callbackStatistics.callbackCount', '回执量')
            ->setHelp('统计小时内收到的回执总数')
            ->onlyOnDetail()
        ;

        yield IntegerField::new('callbackStatistics.callbackSuccessCount', '回执成功量')
            ->setHelp('统计小时内成功的回执数量')
            ->onlyOnDetail()
        ;

        yield IntegerField::new('callbackStatistics.callbackFailCount', '回执失败量')
            ->setHelp('统计小时内失败的回执数量')
            ->onlyOnDetail()
        ;

        yield IntegerField::new('callbackStatistics.internalErrorCount', '内部错误量')
            ->setHelp('统计小时内内部错误的数量')
            ->onlyOnDetail()
        ;

        yield IntegerField::new('callbackStatistics.invalidNumberCount', '无效号码量')
            ->setHelp('统计小时内无效号码的数量')
            ->onlyOnDetail()
        ;

        yield IntegerField::new('callbackStatistics.shutdownErrorCount', '关机量')
            ->setHelp('统计小时内因关机导致的失败数量')
            ->onlyOnDetail()
        ;

        yield IntegerField::new('callbackStatistics.blackListCount', '黑名单量')
            ->setHelp('统计小时内因黑名单导致的失败数量')
            ->onlyOnDetail()
        ;

        yield IntegerField::new('callbackStatistics.frequencyLimitCount', '频率限制量')
            ->setHelp('统计小时内因频率限制导致的失败数量')
            ->onlyOnDetail()
        ;

        // 套餐统计字段
        yield IntegerField::new('packageStatistics.packageAmount', '套餐包条数')
            ->setHelp('套餐包总条数')
            ->hideOnForm()
        ;

        yield IntegerField::new('packageStatistics.usedAmount', '套餐包已用条数')
            ->setHelp('套餐包已使用的条数')
            ->hideOnForm()
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
