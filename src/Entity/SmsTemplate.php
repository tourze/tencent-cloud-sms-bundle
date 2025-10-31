<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TencentCloudSmsBundle\Enum\TemplateReviewStatus;
use TencentCloudSmsBundle\Enum\TemplateType;
use TencentCloudSmsBundle\Repository\SmsTemplateRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;

#[ORM\Table(name: 'tencent_cloud_sms_template', options: ['comment' => '短信模板'])]
#[ORM\Entity(repositoryClass: SmsTemplateRepository::class)]
class SmsTemplate implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, unique: true, options: ['comment' => '模板ID'])]
    private ?string $templateId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '模板名称'])]
    private ?string $templateName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '模板内容'])]
    private ?string $templateContent = null;

    #[Assert\Choice(callback: [TemplateReviewStatus::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: TemplateReviewStatus::class, options: ['comment' => '模板状态'])]
    private ?TemplateReviewStatus $templateStatus = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '审核回复'])]
    private ?string $reviewReply = null;

    #[Assert\Choice(callback: [TemplateType::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: TemplateType::class, options: ['comment' => '模板类型'])]
    private ?TemplateType $templateType = null;

    /**
     * @var array<string, mixed> 模板参数列表
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '模板参数列表'])]
    private array $templateParams = [];

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否国际/港澳台短信'])]
    private bool $international = false;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '模板备注说明'])]
    private ?string $remark = null;

    #[Assert\Type(type: 'bool')]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false, 'comment' => '是否正在同步'])]
    private bool $syncing = false;

    public function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isSyncing(): bool
    {
        return $this->syncing;
    }

    public function setSyncing(bool $syncing): void
    {
        $this->syncing = $syncing;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function setTemplateId(?string $templateId): void
    {
        $this->templateId = $templateId;
    }

    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    public function setTemplateName(?string $templateName): void
    {
        $this->templateName = $templateName;
    }

    public function getTemplateContent(): ?string
    {
        return $this->templateContent;
    }

    public function setTemplateContent(?string $templateContent): void
    {
        $this->templateContent = $templateContent;
    }

    public function getTemplateStatus(): ?TemplateReviewStatus
    {
        return $this->templateStatus;
    }

    public function setTemplateStatus(?TemplateReviewStatus $templateStatus): void
    {
        $this->templateStatus = $templateStatus;
    }

    public function getReviewReply(): ?string
    {
        return $this->reviewReply;
    }

    public function setReviewReply(?string $reviewReply): void
    {
        $this->reviewReply = $reviewReply;
    }

    public function getTemplateType(): ?TemplateType
    {
        return $this->templateType;
    }

    public function setTemplateType(?TemplateType $templateType): void
    {
        $this->templateType = $templateType;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTemplateParams(): array
    {
        return $this->templateParams;
    }

    /**
     * @param array<string, mixed> $templateParams
     */
    public function setTemplateParams(array $templateParams): void
    {
        $this->templateParams = $templateParams;
    }

    public function isInternational(): bool
    {
        return $this->international;
    }

    public function setInternational(bool $international): void
    {
        $this->international = $international;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function __toString(): string
    {
        return sprintf('[%s] %s', $this->templateId, $this->templateName);
    }
}
