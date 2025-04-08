<?php

namespace TencentCloudSmsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum TemplateReviewStatus: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case APPROVED = 0;   // 审核通过且已生效
    case REVIEWING = 1;  // 审核中
    case PENDING = 2;    // 审核通过待生效
    case REJECTED = -1;  // 审核未通过或审核失败

    public function getLabel(): string
    {
        return match ($this) {
            self::APPROVED => '已生效',
            self::REVIEWING => '审核中',
            self::PENDING => '待生效',
            self::REJECTED => '未通过',
        };
    }
}
