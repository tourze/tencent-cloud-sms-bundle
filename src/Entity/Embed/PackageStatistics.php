<?php

namespace TencentCloudSmsBundle\Entity\Embed;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class PackageStatistics
{
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '套餐包条数', 'default' => 0])]
    private int $packageAmount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '套餐包已用条数', 'default' => 0])]
    private int $usedAmount = 0;

    public function getPackageAmount(): int
    {
        return $this->packageAmount;
    }

    public function setPackageAmount(int $packageAmount): void
    {
        $this->packageAmount = $packageAmount;
    }

    public function getUsedAmount(): int
    {
        return $this->usedAmount;
    }

    public function setUsedAmount(int $usedAmount): void
    {
        $this->usedAmount = $usedAmount;
    }
}
