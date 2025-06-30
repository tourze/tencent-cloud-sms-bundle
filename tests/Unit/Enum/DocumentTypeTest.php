<?php

namespace TencentCloudSmsBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Enum\DocumentType;

class DocumentTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, DocumentType::THREE_IN_ONE->value);
        $this->assertSame(1, DocumentType::BUSINESS_LICENSE->value);
        $this->assertSame(2, DocumentType::ORG_CODE->value);
        $this->assertSame(3, DocumentType::SOCIAL_CREDIT->value);
        $this->assertSame(4, DocumentType::APP_ADMIN->value);
        $this->assertSame(5, DocumentType::WEBSITE_ADMIN->value);
        $this->assertSame(6, DocumentType::MINI_PROGRAM->value);
        $this->assertSame(7, DocumentType::TRADEMARK->value);
        $this->assertSame(8, DocumentType::WECHAT_ADMIN->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('三证合一', DocumentType::THREE_IN_ONE->getLabel());
        $this->assertSame('企业营业执照', DocumentType::BUSINESS_LICENSE->getLabel());
        $this->assertSame('组织机构代码证书', DocumentType::ORG_CODE->getLabel());
        $this->assertSame('社会信用代码证书', DocumentType::SOCIAL_CREDIT->getLabel());
        $this->assertSame('应用后台管理截图', DocumentType::APP_ADMIN->getLabel());
        $this->assertSame('网站备案后台截图', DocumentType::WEBSITE_ADMIN->getLabel());
        $this->assertSame('小程序设置页面截图', DocumentType::MINI_PROGRAM->getLabel());
        $this->assertSame('商标注册书', DocumentType::TRADEMARK->getLabel());
        $this->assertSame('公众号设置页面截图', DocumentType::WECHAT_ADMIN->getLabel());
    }

    public function testEnumImplementsExpectedInterfaces(): void
    {
        $enum = DocumentType::THREE_IN_ONE;
        $this->assertInstanceOf('Tourze\\EnumExtra\\Labelable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Itemable', $enum);
        $this->assertInstanceOf('Tourze\\EnumExtra\\Selectable', $enum);
    }
}
