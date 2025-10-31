<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TencentCloudSmsBundle\Enum\MessageStatus;
use TencentCloudSmsBundle\Repository\SmsMessageRepository;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Table(name: 'tencent_cloud_sms_message', options: ['comment' => '短信消息'])]
#[ORM\Entity(repositoryClass: SmsMessageRepository::class)]
class SmsMessage implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, options: ['comment' => '批次号'])]
    private string $batchId;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, options: ['comment' => '短信签名'])]
    private string $signature;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, options: ['comment' => '短信模板ID'])]
    private string $template;

    /**
     * @var array<string, mixed> 模板参数
     */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, options: ['comment' => '模板参数'])]
    private array $templateParams = [];

    #[Assert\Choice(callback: [MessageStatus::class, 'cases'])]
    #[ORM\Column(type: Types::INTEGER, enumType: MessageStatus::class, options: ['comment' => '发送状态'])]
    private ?MessageStatus $status = null;

    #[Assert\Type(type: '\DateTimeImmutable')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    private ?\DateTimeImmutable $sendTime = null;

    /**
     * @var Collection<int, SmsRecipient> 短信接收者列表
     */
    #[ORM\OneToMany(mappedBy: 'message', targetEntity: SmsRecipient::class, cascade: ['persist', 'remove'])]
    private Collection $recipients;

    public function __construct()
    {
        $this->status = MessageStatus::SENDING;
        $this->templateParams = [];
        $this->recipients = new ArrayCollection();
        $this->batchId = substr(md5(uniqid()), 0, 32);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): void
    {
        $this->account = $account;
    }

    public function getBatchId(): string
    {
        return $this->batchId;
    }

    public function setBatchId(string $batchId): void
    {
        $this->batchId = $batchId;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): void
    {
        $this->signature = $signature;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /** @return array<string, mixed> */
    public function getTemplateParams(): array
    {
        return $this->templateParams;
    }

    /** @param array<string, mixed> $templateParams */
    public function setTemplateParams(array $templateParams): void
    {
        $this->templateParams = $templateParams;
    }

    public function getStatus(): ?MessageStatus
    {
        return $this->status;
    }

    public function setStatus(?MessageStatus $status): void
    {
        $this->status = $status;
    }

    public function getSendTime(): ?\DateTimeImmutable
    {
        return $this->sendTime;
    }

    public function setSendTime(?\DateTimeImmutable $sendTime): void
    {
        $this->sendTime = $sendTime;
    }

    /** @return Collection<int, SmsRecipient> */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(SmsRecipient $recipient): void
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
            $recipient->setMessage($this);
        }
    }

    public function removeRecipient(SmsRecipient $recipient): void
    {
        if ($this->recipients->removeElement($recipient)) {
            // set the owning side to null (unless already changed)
            if ($recipient->getMessage() === $this) {
                $recipient->setMessage(null);
            }
        }
    }

    public function __toString(): string
    {
        return sprintf('[%s] %s - %s', $this->batchId, $this->signature, $this->template);
    }
}
