<?php

namespace TencentCloudSmsBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MessageStatus: int implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case SENDING = 0;
    case SUCCESS = 1;
    case FAILED = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::SENDING => '发送中',
            self::SUCCESS => '发送成功',
            self::FAILED => '发送失败',
        };
    }

    public function getBadge(): string
    {
        return $this->getLabel();
    }
}
