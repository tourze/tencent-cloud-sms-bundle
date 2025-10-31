<?php

namespace TencentCloudSmsBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SendStatus: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case SUCCESS = 'success';            // 短信发送成功
    case FAIL = 'fail';                  // 短信发送失败
    case RATE_LIMIT_EXCEED = 'exceed';   // 短信发送频率限制
    case PHONE_NUMBER_LIMIT = 'limit';   // 手机号码在免打扰名单中
    case INSUFFICIENT_PACKAGE = 'insufficient'; // 套餐包余量不足

    public function getLabel(): string
    {
        return match ($this) {
            self::SUCCESS => '发送成功',
            self::FAIL => '发送失败',
            self::RATE_LIMIT_EXCEED => '频率限制',
            self::PHONE_NUMBER_LIMIT => '免打扰名单',
            self::INSUFFICIENT_PACKAGE => '余量不足',
        };
    }

    public function getBadge(): string
    {
        return $this->getLabel();
    }
}
