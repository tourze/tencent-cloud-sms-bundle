<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use TencentCloudSmsBundle\Enum\TemplateReviewStatus;
use TencentCloudSmsBundle\Enum\TemplateType;
use TencentCloudSmsBundle\Repository\SmsTemplateRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '短信模板管理')]
#[ORM\Table(name: 'tencent_cloud_sms_template', options: ['comment' => '短信模板'])]
#[ORM\Entity(repositoryClass: SmsTemplateRepository::class)]
class SmsTemplate
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ListColumn]
    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ListColumn]
    #[ORM\Column(length: 20, unique: true, options: ['comment' => '模板ID'])]
    private ?string $templateId = null;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '模板名称'])]
    private ?string $templateName = null;

    #[ListColumn]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '模板内容'])]
    private ?string $templateContent = null;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, enumType: TemplateReviewStatus::class, options: ['comment' => '模板状态'])]
    private ?TemplateReviewStatus $templateStatus = null;

    #[ListColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '审核回复'])]
    private ?string $reviewReply = null;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, enumType: TemplateType::class, options: ['comment' => '模板类型'])]
    private ?TemplateType $templateType = null;

    #[ListColumn]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '模板参数列表'])]
    private array $templateParams = [];

    #[ListColumn]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否国际/港澳台短信'])]
    private bool $international = false;

    #[ListColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '模板备注说明'])]
    private ?string $remark = null;

    #[ORM\OneToMany(mappedBy: 'template', targetEntity: SmsMessage::class)]
    private Collection $messages;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[Groups(['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

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

    private bool $syncing = false;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;
        return $this;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function setTemplateId(?string $templateId): static
    {
        $this->templateId = $templateId;
        return $this;
    }

    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    public function setTemplateName(?string $templateName): static
    {
        $this->templateName = $templateName;
        return $this;
    }

    public function getTemplateContent(): ?string
    {
        return $this->templateContent;
    }

    public function setTemplateContent(?string $templateContent): static
    {
        $this->templateContent = $templateContent;
        return $this;
    }

    public function getTemplateStatus(): ?TemplateReviewStatus
    {
        return $this->templateStatus;
    }

    public function setTemplateStatus(?TemplateReviewStatus $templateStatus): static
    {
        $this->templateStatus = $templateStatus;
        return $this;
    }

    public function getReviewReply(): ?string
    {
        return $this->reviewReply;
    }

    public function setReviewReply(?string $reviewReply): static
    {
        $this->reviewReply = $reviewReply;
        return $this;
    }

    public function getTemplateType(): ?TemplateType
    {
        return $this->templateType;
    }

    public function setTemplateType(?TemplateType $templateType): static
    {
        $this->templateType = $templateType;
        return $this;
    }

    public function getTemplateParams(): array
    {
        return $this->templateParams;
    }

    public function setTemplateParams(array $templateParams): static
    {
        $this->templateParams = $templateParams;
        return $this;
    }

    public function isInternational(): bool
    {
        return $this->international;
    }

    public function setInternational(bool $international): static
    {
        $this->international = $international;
        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): static
    {
        $this->remark = $remark;
        return $this;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(SmsMessage $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setTemplate($this);
        }

        return $this;
    }

    public function removeMessage(SmsMessage $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getTemplate() === $this) {
                $message->setTemplate(null);
            }
        }

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

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
