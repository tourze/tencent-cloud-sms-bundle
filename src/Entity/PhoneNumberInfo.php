<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use TencentCloudSmsBundle\Repository\PhoneNumberInfoRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '手机号信息')]
#[ORM\Table(name: 'tencent_cloud_sms_phone_number_info', options: ['comment' => '手机号码信息'])]
#[ORM\Entity(repositoryClass: PhoneNumberInfoRepository::class)]
class PhoneNumberInfo
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ListColumn]
    #[ORM\Column(length: 20, unique: true, options: ['comment' => '手机号码'])]
    private string $phoneNumber;

    #[IndexColumn]
    #[ListColumn]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '国家码'])]
    private ?string $nationCode = null;

    #[ListColumn]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '国家/地区ISO编码'])]
    private ?string $isoCode = null;

    #[ListColumn]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '国家/地区名称'])]
    private ?string $isoName = null;

    #[ListColumn]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '用户号码'])]
    private ?string $subscriberNumber = null;

    #[ListColumn]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '完整号码'])]
    private ?string $fullNumber = null;

    #[ListColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '查询结果'])]
    private ?string $message = null;

    #[ListColumn]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '查询状态码'])]
    private ?string $code = null;

    private bool $syncing = false;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function needSync(): bool
    {
        return $this->nationCode === null;
    }

    public function isSyncing(): bool
    {
        return $this->syncing;
    }

    public function setSyncing(bool $syncing): static
    {
        $this->syncing = $syncing;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getNationCode(): ?string
    {
        return $this->nationCode;
    }

    public function setNationCode(?string $nationCode): static
    {
        $this->nationCode = $nationCode;
        return $this;
    }

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function setIsoCode(?string $isoCode): static
    {
        $this->isoCode = $isoCode;
        return $this;
    }

    public function getIsoName(): ?string
    {
        return $this->isoName;
    }

    public function setIsoName(?string $isoName): static
    {
        $this->isoName = $isoName;
        return $this;
    }

    public function getSubscriberNumber(): ?string
    {
        return $this->subscriberNumber;
    }

    public function setSubscriberNumber(?string $subscriberNumber): static
    {
        $this->subscriberNumber = $subscriberNumber;
        return $this;
    }

    public function getFullNumber(): ?string
    {
        return $this->fullNumber;
    }

    public function setFullNumber(?string $fullNumber): static
    {
        $this->fullNumber = $fullNumber;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;
        return $this;
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
