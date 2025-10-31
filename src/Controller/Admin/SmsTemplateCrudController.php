<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\Enum\TemplateReviewStatus;
use TencentCloudSmsBundle\Enum\TemplateType;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

#[AdminCrud(routePath: '/tencent-sms/template', routeName: 'tencent_sms_template')]
final class SmsTemplateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SmsTemplate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('短信模板')
            ->setEntityLabelInPlural('短信模板管理')
            ->setSearchFields(['templateId', 'templateName', 'templateContent'])
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('account')
            ->add('templateId')
            ->add('templateName')
            ->add('templateContent')
            ->add('templateType')
            ->add('templateStatus')
            ->add('international')
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

        yield TextField::new('templateId', '模板ID')
            ->setHelp('腾讯云短信模板的唯一标识符，最多20个字符')
        ;

        yield TextField::new('templateName', '模板名称')
            ->setRequired(true)
            ->setHelp('短信模板的显示名称，最多100个字符')
        ;

        yield TextareaField::new('templateContent', '模板内容')
            ->setRequired(true)
            ->setHelp('短信模板的具体内容，支持参数变量')
            ->hideOnIndex()
        ;

        $templateTypeField = EnumField::new('templateType', '模板类型');
        $templateTypeField->setEnumCases(TemplateType::cases());
        $templateTypeField->setRequired(true);
        $templateTypeField->setHelp('短信模板的类型分类');
        yield $templateTypeField;

        $templateStatusField = EnumField::new('templateStatus', '审核状态');
        $templateStatusField->setEnumCases(TemplateReviewStatus::cases());
        $templateStatusField->setHelp('模板的审核状态');
        yield $templateStatusField;

        yield TextareaField::new('reviewReply', '审核回复')
            ->setHelp('腾讯云审核人员的回复内容')
            ->hideOnIndex()
        ;

        yield ArrayField::new('templateParams', '模板参数')
            ->setHelp('模板中使用的参数列表')
            ->hideOnIndex()
        ;

        yield BooleanField::new('international', '国际短信')
            ->setHelp('是否为国际/港澳台短信模板')
        ;

        yield BooleanField::new('valid', '是否有效')
            ->setHelp('标记此模板是否可用')
        ;

        yield BooleanField::new('syncing', '同步中')
            ->setHelp('是否正在与腾讯云同步')
            ->onlyOnIndex()
        ;

        yield TextareaField::new('remark', '备注说明')
            ->setHelp('模板的备注信息')
            ->hideOnIndex()
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
