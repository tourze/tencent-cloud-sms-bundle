<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TencentCloudSmsBundle\Repository\PhoneNumberInfoRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Table(name: 'tencent_cloud_sms_phone_number_info', options: ['comment' => '手机号码信息'])]
#[ORM\Entity(repositoryClass: PhoneNumberInfoRepository::class)]
class PhoneNumberInfo implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Assert\Regex(pattern: '/^\+?[1-9]\d{1,14}$/', message: 'Invalid phone number format')]
    #[ORM\Column(length: 20, unique: true, options: ['comment' => '手机号码'])]
    private string $phoneNumber;

    #[Assert\Length(max: 20)]
    #[IndexColumn]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '国家码'])]
    private ?string $nationCode = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '国家/地区ISO编码'])]
    private ?string $isoCode = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '国家/地区名称'])]
    private ?string $isoName = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '用户号码'])]
    private ?string $subscriberNumber = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '完整号码'])]
    private ?string $fullNumber = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '查询结果'])]
    private ?string $message = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, nullable: true, options: ['comment' => '查询状态码'])]
    private ?string $code = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false, 'comment' => '是否正在同步'])]
    private bool $syncing = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function needSync(): bool
    {
        return null === $this->nationCode;
    }

    public function isSyncing(): bool
    {
        return $this->syncing;
    }

    public function setSyncing(bool $syncing): void
    {
        $this->syncing = $syncing;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getNationCode(): ?string
    {
        return $this->nationCode;
    }

    public function setNationCode(?string $nationCode): void
    {
        $this->nationCode = $nationCode;
    }

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function setIsoCode(?string $isoCode): void
    {
        $this->isoCode = $isoCode;
    }

    public function getIsoName(): ?string
    {
        return $this->isoName;
    }

    public function setIsoName(?string $isoName): void
    {
        $this->isoName = $isoName;
    }

    public function getSubscriberNumber(): ?string
    {
        return $this->subscriberNumber;
    }

    public function setSubscriberNumber(?string $subscriberNumber): void
    {
        $this->subscriberNumber = $subscriberNumber;
    }

    public function getFullNumber(): ?string
    {
        return $this->fullNumber;
    }

    public function setFullNumber(?string $fullNumber): void
    {
        $this->fullNumber = $fullNumber;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function __toString(): string
    {
        return sprintf('PhoneInfo[%s:%s]', $this->phoneNumber, $this->nationCode);
    }
}
