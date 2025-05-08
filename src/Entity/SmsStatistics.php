<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use TencentCloudSmsBundle\Entity\Embed\CallbackStatistics;
use TencentCloudSmsBundle\Entity\Embed\PackageStatistics;
use TencentCloudSmsBundle\Entity\Embed\SendStatistics;
use TencentCloudSmsBundle\Repository\SmsStatisticsRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;

#[ORM\Entity(repositoryClass: SmsStatisticsRepository::class)]
#[ORM\Table(name: 'tcs_sms_statistics')]
#[ORM\UniqueConstraint(name: 'uniq_hour_account', columns: ['hour', 'account_id'])]
class SmsStatistics
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '统计小时'])]
    private \DateTimeImmutable $hour;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(name: 'account_id', referencedColumnName: 'id', nullable: false)]
    private Account $account;

    #[ORM\Embedded(class: SendStatistics::class, columnPrefix: 'send_')]
    private SendStatistics $sendStatistics;

    #[ORM\Embedded(class: CallbackStatistics::class, columnPrefix: 'callback_')]
    private CallbackStatistics $callbackStatistics;

    #[ORM\Embedded(class: PackageStatistics::class, columnPrefix: 'package_')]
    private PackageStatistics $packageStatistics;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __construct()
    {
        $this->sendStatistics = new SendStatistics();
        $this->callbackStatistics = new CallbackStatistics();
        $this->packageStatistics = new PackageStatistics();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHour(): \DateTimeImmutable
    {
        return $this->hour;
    }

    public function setHour(\DateTimeImmutable $hour): self
    {
        $this->hour = $hour;
        return $this;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): self
    {
        $this->account = $account;
        return $this;
    }

    public function getSendStatistics(): SendStatistics
    {
        return $this->sendStatistics;
    }

    public function getCallbackStatistics(): CallbackStatistics
    {
        return $this->callbackStatistics;
    }

    public function getPackageStatistics(): PackageStatistics
    {
        return $this->packageStatistics;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }
}
