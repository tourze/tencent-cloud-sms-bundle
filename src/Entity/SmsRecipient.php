<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TencentCloudSmsBundle\Enum\SendStatus;
use TencentCloudSmsBundle\Repository\SmsRecipientRepository;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Table(name: 'tencent_cloud_sms_recipient', options: ['comment' => '短信接收人'])]
#[ORM\Entity(repositoryClass: SmsRecipientRepository::class)]
class SmsRecipient implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotNull(message: '短信消息不能为空')]
    #[ORM\ManyToOne(targetEntity: SmsMessage::class, inversedBy: 'recipients', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?SmsMessage $message = null;

    #[Assert\NotNull(message: '手机号信息不能为空')]
    #[ORM\ManyToOne(targetEntity: PhoneNumberInfo::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?PhoneNumberInfo $phoneNumber = null;

    #[Assert\Choice(callback: [SendStatus::class, 'cases'])]
    #[ORM\Column(type: Types::STRING, enumType: SendStatus::class, nullable: true, options: ['comment' => '发送状态'])]
    private ?SendStatus $status = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '腾讯云返回的序列号'])]
    private ?string $serialNo = null;

    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '计费条数'])]
    private ?int $fee = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '腾讯云状态码'])]
    private ?string $code = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '腾讯云返回消息'])]
    private ?string $statusMessage = null;

    #[Assert\Type(type: '\DateTimeImmutable')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    private ?\DateTimeImmutable $sendTime = null;

    #[Assert\Type(type: '\DateTimeImmutable')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '接收时间'])]
    private ?\DateTimeImmutable $receiveTime = null;

    #[Assert\Type(type: '\DateTimeImmutable')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '状态更新时间'])]
    private ?\DateTimeImmutable $statusTime = null;

    /** @var array<string, mixed>|null */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '腾讯云原始响应'])]
    private ?array $rawResponse = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getMessage(): ?SmsMessage
    {
        return $this->message;
    }

    public function setMessage(?SmsMessage $message): void
    {
        $this->message = $message;
    }

    public function getPhoneNumber(): ?PhoneNumberInfo
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumberInfo $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getStatus(): ?SendStatus
    {
        return $this->status;
    }

    public function setStatus(?SendStatus $status): void
    {
        $this->status = $status;
    }

    public function getSerialNo(): ?string
    {
        return $this->serialNo;
    }

    public function setSerialNo(?string $serialNo): void
    {
        $this->serialNo = $serialNo;
    }

    public function getFee(): ?int
    {
        return $this->fee;
    }

    public function setFee(?int $fee): void
    {
        $this->fee = $fee;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getStatusMessage(): ?string
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(?string $statusMessage): void
    {
        $this->statusMessage = $statusMessage;
    }

    public function getSendTime(): ?\DateTimeImmutable
    {
        return $this->sendTime;
    }

    public function setSendTime(?\DateTimeImmutable $sendTime): void
    {
        $this->sendTime = $sendTime;
    }

    public function getReceiveTime(): ?\DateTimeImmutable
    {
        return $this->receiveTime;
    }

    public function setReceiveTime(?\DateTimeImmutable $receiveTime): void
    {
        $this->receiveTime = $receiveTime;
    }

    public function getStatusTime(): ?\DateTimeImmutable
    {
        return $this->statusTime;
    }

    public function setStatusTime(?\DateTimeImmutable $statusTime): void
    {
        $this->statusTime = $statusTime;
    }

    /** @return array<string, mixed>|null */
    public function getRawResponse(): ?array
    {
        return $this->rawResponse;
    }

    /** @param array<string, mixed>|null $rawResponse */
    public function setRawResponse(?array $rawResponse): void
    {
        $this->rawResponse = $rawResponse;
    }

    public function __toString(): string
    {
        return null !== $this->phoneNumber ? (string) $this->phoneNumber : '';
    }
}
