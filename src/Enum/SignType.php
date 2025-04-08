<?php

namespace TencentCloudSmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SignType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case COMPANY = 0;
    case APP = 1;
    case WEBSITE = 2;
    case WECHAT = 3;
    case TRADEMARK = 4;
    case GOVERNMENT = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::COMPANY => '公司',
            self::APP => 'APP',
            self::WEBSITE => '网站',
            self::WECHAT => '公众号',
            self::TRADEMARK => '商标',
            self::GOVERNMENT => '政府/机关事业单位/其他机构',
        };
    }
}
