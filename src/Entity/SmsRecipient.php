<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use TencentCloudSmsBundle\Enum\SendStatus;
use TencentCloudSmsBundle\Repository\SmsRecipientRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Table(name: 'tencent_cloud_sms_recipient', options: ['comment' => '短信接收人'])]
#[ORM\Entity(repositoryClass: SmsRecipientRepository::class)]
class SmsRecipient implements \Stringable
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[IndexColumn]
    #[ORM\ManyToOne(targetEntity: SmsMessage::class, inversedBy: 'recipients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SmsMessage $message = null;

    #[IndexColumn]
    #[ORM\ManyToOne(targetEntity: PhoneNumberInfo::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?PhoneNumberInfo $phoneNumber = null;

    #[ORM\Column(type: Types::STRING, enumType: SendStatus::class, options: ['comment' => '发送状态'])]
    private ?SendStatus $status = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '腾讯云返回的序列号'])]
    private ?string $serialNo = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '计费条数'])]
    private ?int $fee = null;

    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '腾讯云状态码'])]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '腾讯云返回消息'])]
    private ?string $statusMessage = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    private ?\DateTimeImmutable $sendTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '接收时间'])]
    private ?\DateTimeImmutable $receiveTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '状态更新时间'])]
    private ?\DateTimeImmutable $statusTime = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '腾讯云原始响应'])]
    private ?array $rawResponse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?SmsMessage
    {
        return $this->message;
    }

    public function setMessage(?SmsMessage $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getPhoneNumber(): ?PhoneNumberInfo
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumberInfo $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getStatus(): ?SendStatus
    {
        return $this->status;
    }

    public function setStatus(?SendStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getSerialNo(): ?string
    {
        return $this->serialNo;
    }

    public function setSerialNo(?string $serialNo): static
    {
        $this->serialNo = $serialNo;
        return $this;
    }

    public function getFee(): ?int
    {
        return $this->fee;
    }

    public function setFee(?int $fee): static
    {
        $this->fee = $fee;
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

    public function getStatusMessage(): ?string
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(?string $statusMessage): static
    {
        $this->statusMessage = $statusMessage;
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

    public function getReceiveTime(): ?\DateTimeImmutable
    {
        return $this->receiveTime;
    }

    public function setReceiveTime(?\DateTimeImmutable $receiveTime): static
    {
        $this->receiveTime = $receiveTime;
        return $this;
    }

    public function getStatusTime(): ?\DateTimeImmutable
    {
        return $this->statusTime;
    }

    public function setStatusTime(?\DateTimeImmutable $statusTime): static
    {
        $this->statusTime = $statusTime;
        return $this;
    }

    public function getRawResponse(): ?array
    {
        return $this->rawResponse;
    }

    public function setRawResponse(?array $rawResponse): static
    {
        $this->rawResponse = $rawResponse;
        return $this;
    }

    public function __toString(): string
    {
        return $this->phoneNumber !== null ? (string) $this->phoneNumber : '';
    }
}
