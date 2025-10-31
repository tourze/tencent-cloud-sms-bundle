<?php

namespace TencentCloudSmsBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SignPurpose: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case SELF_USE = 0;  // 自用
    case OTHER_USE = 1; // 他用

    public function getLabel(): string
    {
        return match ($this) {
            self::SELF_USE => '自用',
            self::OTHER_USE => '他用',
        };
    }

    public function getBadge(): string
    {
        return $this->getLabel();
    }
}
