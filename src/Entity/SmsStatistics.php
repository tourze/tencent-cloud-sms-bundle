<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use TencentCloudSmsBundle\Entity\Embed\CallbackStatistics;
use TencentCloudSmsBundle\Entity\Embed\PackageStatistics;
use TencentCloudSmsBundle\Entity\Embed\SendStatistics;
use TencentCloudSmsBundle\Repository\SmsStatisticsRepository;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: SmsStatisticsRepository::class)]
#[ORM\Table(name: 'tcs_sms_statistics', options: ['comment' => '短信统计'])]
#[ORM\UniqueConstraint(name: 'uniq_hour_account', columns: ['hour', 'account_id'])]
class SmsStatistics implements \Stringable
{
    use TimestampableAware;
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

    public function __toString(): string
    {
        return sprintf('[%s] %s', $this->hour->format('Y-m-d H:i'), $this->account->getName());
    }
}
