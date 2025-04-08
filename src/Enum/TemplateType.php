<?php

namespace TencentCloudSmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum TemplateType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case REGULAR = 0;
    case MARKETING = 1;
    case NOTIFICATION = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::REGULAR => '普通短信',
            self::MARKETING => '营销短信',
            self::NOTIFICATION => '通知类短信',
        };
    }
}
