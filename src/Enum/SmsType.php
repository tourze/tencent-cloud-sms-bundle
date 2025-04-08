<?php

namespace TencentCloudSmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SmsType: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case MAINLAND = 0;
    case INTERNATIONAL = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::MAINLAND => '国内短信',
            self::INTERNATIONAL => '国际/港澳台短信',
        };
    }
}
