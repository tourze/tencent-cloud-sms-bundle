<?php

namespace TencentCloudSmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum DocumentType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case THREE_IN_ONE = 0;            // 三证合一
    case BUSINESS_LICENSE = 1;        // 企业营业执照
    case ORG_CODE = 2;               // 组织机构代码证书
    case SOCIAL_CREDIT = 3;          // 社会信用代码证书
    case APP_ADMIN = 4;              // 应用后台管理截图（个人开发APP）
    case WEBSITE_ADMIN = 5;          // 网站备案后台截图（个人开发网站）
    case MINI_PROGRAM = 6;           // 小程序设置页面截图（个人认证小程序）
    case TRADEMARK = 7;              // 商标注册书
    case WECHAT_ADMIN = 8;           // 公众号设置页面截图（个人认证公众号）

    public function getLabel(): string
    {
        return match ($this) {
            self::THREE_IN_ONE => '三证合一',
            self::BUSINESS_LICENSE => '企业营业执照',
            self::ORG_CODE => '组织机构代码证书',
            self::SOCIAL_CREDIT => '社会信用代码证书',
            self::APP_ADMIN => '应用后台管理截图',
            self::WEBSITE_ADMIN => '网站备案后台截图',
            self::MINI_PROGRAM => '小程序设置页面截图',
            self::TRADEMARK => '商标注册书',
            self::WECHAT_ADMIN => '公众号设置页面截图',
        };
    }
}
