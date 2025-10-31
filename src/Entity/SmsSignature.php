<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
class SmsSignature implements \Stringable
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
    #[ORM\Column(length: 20, unique: true, options: ['comment' => '签名ID'])]
    private ?string $signId = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '签名名称'])]
    private ?string $signName = null;

    #[Assert\Choice(callback: [SignType::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: SignType::class, options: ['comment' => '签名类型'])]
    private ?SignType $signType = null;

    #[Assert\Choice(callback: [DocumentType::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: DocumentType::class, options: ['comment' => '证明类型'])]
    private ?DocumentType $documentType = null;

    #[Assert\Length(max: 255)]
    #[Assert\Url]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '证明文件'])]
    private ?string $documentUrl = null;

    #[Assert\Choice(callback: [SignReviewStatus::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: SignReviewStatus::class, options: ['comment' => '签名状态'])]
    private ?SignReviewStatus $signStatus = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '审核回复'])]
    private ?string $reviewReply = null;

    #[Assert\Type(type: 'bool')]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否国际/港澳台短信'])]
    private bool $international = false;

    #[Assert\Choice(callback: [SignPurpose::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: SignPurpose::class, options: ['comment' => '签名用途'])]
    private ?SignPurpose $signPurpose = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '签名内容'])]
    private ?string $signContent = null;

    /**
     * @var Collection<int, SmsMessage> 签名关联的短信消息
     */
    #[ORM\OneToMany(mappedBy: 'signature', targetEntity: SmsMessage::class)]
    private Collection $messages;

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
        $this->signStatus = SignReviewStatus::REVIEWING;
        $this->messages = new ArrayCollection();
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

    public function getSignId(): ?string
    {
        return $this->signId;
    }

    public function setSignId(?string $signId): void
    {
        $this->signId = $signId;
    }

    public function getSignName(): ?string
    {
        return $this->signName;
    }

    public function setSignName(?string $signName): void
    {
        $this->signName = $signName;
    }

    public function getSignType(): ?SignType
    {
        return $this->signType;
    }

    public function setSignType(?SignType $signType): void
    {
        $this->signType = $signType;
    }

    public function getDocumentType(): ?DocumentType
    {
        return $this->documentType;
    }

    public function setDocumentType(?DocumentType $documentType): void
    {
        $this->documentType = $documentType;
    }

    public function getDocumentUrl(): ?string
    {
        return $this->documentUrl;
    }

    public function setDocumentUrl(?string $documentUrl): void
    {
        $this->documentUrl = $documentUrl;
    }

    public function getSignStatus(): ?SignReviewStatus
    {
        return $this->signStatus;
    }

    public function setSignStatus(?SignReviewStatus $signStatus): void
    {
        $this->signStatus = $signStatus;
    }

    public function getReviewReply(): ?string
    {
        return $this->reviewReply;
    }

    public function setReviewReply(?string $reviewReply): void
    {
        $this->reviewReply = $reviewReply;
    }

    public function isInternational(): bool
    {
        return $this->international;
    }

    public function setInternational(bool $international): void
    {
        $this->international = $international;
    }

    public function getSignPurpose(): ?SignPurpose
    {
        return $this->signPurpose;
    }

    public function setSignPurpose(?SignPurpose $signPurpose): void
    {
        $this->signPurpose = $signPurpose;
    }

    public function getSignContent(): ?string
    {
        return $this->signContent;
    }

    public function setSignContent(?string $signContent): void
    {
        $this->signContent = $signContent;
    }

    /**
     * @return Collection<int, SmsMessage>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(SmsMessage $message): void
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSignature($this->signName ?? '');
        }
    }

    public function removeMessage(SmsMessage $message): void
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSignature() === ($this->signName ?? '')) {
                $message->setSignature('');
            }
        }
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
        return $this->signName ?? '';
    }
}
