<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Enum\DocumentType;
use TencentCloudSmsBundle\Enum\SignPurpose;
use TencentCloudSmsBundle\Enum\SignReviewStatus;
use TencentCloudSmsBundle\Enum\SignType;
use Tourze\EasyAdminEnumFieldBundle\Field\EnumField;

#[AdminCrud(routePath: '/tencent-sms/signature', routeName: 'tencent_sms_signature')]
final class SmsSignatureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SmsSignature::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('短信签名')
            ->setEntityLabelInPlural('短信签名管理')
            ->setSearchFields(['signId', 'signName', 'signContent'])
            ->setDefaultSort(['id' => 'DESC'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('account')
            ->add('signId')
            ->add('signName')
            ->add('signContent')
            ->add('signType')
            ->add('signStatus')
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

        yield TextField::new('signId', '签名ID')
            ->setHelp('腾讯云系统生成的签名唯一标识符')
            ->hideOnForm()
        ;

        yield TextField::new('signName', '签名名称')
            ->setRequired(true)
            ->setHelp('短信签名的名称，最多100个字符')
        ;

        $signTypeField = EnumField::new('signType', '签名类型');
        $signTypeField->setEnumCases(SignType::cases());
        $signTypeField->setRequired(true);
        $signTypeField->setHelp('选择签名的类型');
        yield $signTypeField;

        $documentTypeField = EnumField::new('documentType', '证明类型');
        $documentTypeField->setEnumCases(DocumentType::cases());
        $documentTypeField->setRequired(true);
        $documentTypeField->setHelp('选择证明文件的类型');
        yield $documentTypeField;

        yield UrlField::new('documentUrl', '证明文件URL')
            ->setRequired(true)
            ->setHelp('上传证明文件的URL地址')
            ->hideOnIndex()
        ;

        $signPurposeField = EnumField::new('signPurpose', '签名用途');
        $signPurposeField->setEnumCases(SignPurpose::cases());
        $signPurposeField->setRequired(true);
        $signPurposeField->setHelp('选择签名的使用用途');
        yield $signPurposeField;

        yield BooleanField::new('international', '国际/港澳台')
            ->setHelp('是否用于国际或港澳台短信发送')
        ;

        $signStatusField = EnumField::new('signStatus', '审核状态');
        $signStatusField->setEnumCases(SignReviewStatus::cases());
        $signStatusField->setHelp('签名的当前审核状态');
        yield $signStatusField;

        yield TextareaField::new('signContent', '签名内容')
            ->setHelp('签名的具体内容描述')
            ->hideOnIndex()
        ;

        yield TextareaField::new('reviewReply', '审核回复')
            ->setHelp('腾讯云审核的回复信息')
            ->hideOnIndex()
            ->hideOnForm()
        ;

        yield BooleanField::new('valid', '是否有效')
            ->setHelp('标记此签名是否可用')
        ;

        yield BooleanField::new('syncing', '正在同步')
            ->setHelp('是否正在与腾讯云同步')
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
