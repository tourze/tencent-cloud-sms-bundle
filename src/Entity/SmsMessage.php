<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use TencentCloudSmsBundle\Enum\MessageStatus;
use TencentCloudSmsBundle\Repository\SmsMessageRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Table(name: 'tencent_cloud_sms_message', options: ['comment' => '短信消息'])]
#[ORM\Entity(repositoryClass: SmsMessageRepository::class)]
class SmsMessage implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[IndexColumn]
    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\Column(length: 50, options: ['comment' => '批次号'])]
    private string $batchId;

    #[ORM\Column(length: 50, options: ['comment' => '短信签名'])]
    private string $signature;

    #[ORM\Column(length: 50, options: ['comment' => '短信模板ID'])]
    private string $template;

    #[ORM\Column(type: Types::JSON, options: ['comment' => '模板参数'])]
    private array $templateParams = [];

    #[ORM\Column(type: Types::STRING, enumType: MessageStatus::class, options: ['comment' => '发送状态'])]
    private ?MessageStatus $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    private ?\DateTimeImmutable $sendTime = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: SmsRecipient::class, cascade: ['persist', 'remove'])]
    private Collection $recipients;

    public function __construct()
    {
        $this->status = MessageStatus::SENDING;
        $this->templateParams = [];
        $this->recipients = new ArrayCollection();
        $this->batchId = substr(md5(uniqid()), 0, 32);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBatchId(): string
    {
        return $this->batchId;
    }

    public function setBatchId(string $batchId): static
    {
        $this->batchId = $batchId;
        return $this;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): static
    {
        $this->signature = $signature;
        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): static
    {
        $this->template = $template;
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

    public function getStatus(): ?MessageStatus
    {
        return $this->status;
    }

    public function setStatus(?MessageStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getSendTime(): ?\DateTimeImmutable
    {
        return $this->sendTime;
    }

    public function setSendTime(?\DateTimeImmutable $sendTime): static
    {
        $this->sendTime = $sendTime;
        return $this;
    }

    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(SmsRecipient $recipient): static
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
            $recipient->setMessage($this);
        }

        return $this;
    }

    public function removeRecipient(SmsRecipient $recipient): static
    {
        if ($this->recipients->removeElement($recipient)) {
            // set the owning side to null (unless already changed)
            if ($recipient->getMessage() === $this) {
                $recipient->setMessage(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('[%s] %s - %s', $this->batchId, $this->signature, $this->template);
    }
}
