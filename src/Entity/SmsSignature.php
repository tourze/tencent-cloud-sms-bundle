<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use TencentCloudSmsBundle\Enum\DocumentType;
use TencentCloudSmsBundle\Enum\SignPurpose;
use TencentCloudSmsBundle\Enum\SignReviewStatus;
use TencentCloudSmsBundle\Enum\SignType;
use TencentCloudSmsBundle\Repository\SmsSignatureRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;

#[ORM\Table(name: 'tencent_cloud_sms_signature', options: ['comment' => '短信签名'])]
#[ORM\Entity(repositoryClass: SmsSignatureRepository::class)]
class SmsSignature
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\Column(length: 20, unique: true, options: ['comment' => '签名ID'])]
    private ?string $signId = null;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '签名名称'])]
    private ?string $signName = null;

    #[ORM\Column(type: Types::STRING, enumType: SignType::class, options: ['comment' => '签名类型'])]
    private ?SignType $signType = null;

    #[ORM\Column(type: Types::STRING, enumType: DocumentType::class, options: ['comment' => '证明类型'])]
    private ?DocumentType $documentType = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '证明文件'])]
    private ?string $documentUrl = null;

    #[ORM\Column(type: Types::STRING, enumType: SignReviewStatus::class, options: ['comment' => '签名状态'])]
    private ?SignReviewStatus $signStatus = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '审核回复'])]
    private ?string $reviewReply = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否国际/港澳台短信'])]
    private bool $international = false;

    #[ORM\Column(type: Types::STRING, enumType: SignPurpose::class, options: ['comment' => '签名用途'])]
    private ?SignPurpose $signPurpose = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '签名内容'])]
    private ?string $signContent = null;

    #[ORM\OneToMany(mappedBy: 'signature', targetEntity: SmsMessage::class)]
    private Collection $messages;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    private bool $syncing = false;

    public function __construct()
    {
        $this->signStatus = SignReviewStatus::REVIEWING;
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

    public function getSignId(): ?string
    {
        return $this->signId;
    }

    public function setSignId(?string $signId): static
    {
        $this->signId = $signId;
        return $this;
    }

    public function getSignName(): ?string
    {
        return $this->signName;
    }

    public function setSignName(?string $signName): static
    {
        $this->signName = $signName;
        return $this;
    }

    public function getSignType(): ?SignType
    {
        return $this->signType;
    }

    public function setSignType(?SignType $signType): static
    {
        $this->signType = $signType;
        return $this;
    }

    public function getDocumentType(): ?DocumentType
    {
        return $this->documentType;
    }

    public function setDocumentType(?DocumentType $documentType): static
    {
        $this->documentType = $documentType;
        return $this;
    }

    public function getDocumentUrl(): ?string
    {
        return $this->documentUrl;
    }

    public function setDocumentUrl(?string $documentUrl): static
    {
        $this->documentUrl = $documentUrl;
        return $this;
    }

    public function getSignStatus(): ?SignReviewStatus
    {
        return $this->signStatus;
    }

    public function setSignStatus(?SignReviewStatus $signStatus): static
    {
        $this->signStatus = $signStatus;
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

    public function isInternational(): bool
    {
        return $this->international;
    }

    public function setInternational(bool $international): static
    {
        $this->international = $international;
        return $this;
    }

    public function getSignPurpose(): ?SignPurpose
    {
        return $this->signPurpose;
    }

    public function setSignPurpose(?SignPurpose $signPurpose): static
    {
        $this->signPurpose = $signPurpose;
        return $this;
    }

    public function getSignContent(): ?string
    {
        return $this->signContent;
    }

    public function setSignContent(?string $signContent): static
    {
        $this->signContent = $signContent;
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
            $message->setSignature($this);
        }

        return $this;
    }

    public function removeMessage(SmsMessage $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSignature() === $this) {
                $message->setSignature(null);
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
    }}
