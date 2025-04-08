<?php

namespace TencentCloudSmsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use TencentCloudSmsBundle\Enum\SendStatus;
use TencentCloudSmsBundle\Repository\SmsRecipientRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '短信接收人')]
#[ORM\Table(name: 'tencent_cloud_sms_recipient', options: ['comment' => '短信接收人'])]
#[ORM\Entity(repositoryClass: SmsRecipientRepository::class)]
class SmsRecipient
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[IndexColumn]
    #[ListColumn]
    #[ORM\ManyToOne(targetEntity: SmsMessage::class, inversedBy: 'recipients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SmsMessage $message = null;

    #[IndexColumn]
    #[ListColumn]
    #[ORM\ManyToOne(targetEntity: PhoneNumberInfo::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?PhoneNumberInfo $phoneNumber = null;

    #[ListColumn]
    #[ORM\Column(type: Types::STRING, enumType: SendStatus::class, options: ['comment' => '发送状态'])]
    private ?SendStatus $status = null;

    #[ListColumn]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '腾讯云返回的序列号'])]
    private ?string $serialNo = null;

    #[ListColumn]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '计费条数'])]
    private ?int $fee = null;

    #[ListColumn]
    #[ORM\Column(length: 50, nullable: true, options: ['comment' => '腾讯云状态码'])]
    private ?string $code = null;

    #[ListColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '腾讯云返回消息'])]
    private ?string $statusMessage = null;

    #[ListColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    private ?\DateTimeInterface $sendTime = null;

    #[ListColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '接收时间'])]
    private ?\DateTimeInterface $receiveTime = null;

    #[ListColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '状态更新时间'])]
    private ?\DateTimeInterface $statusTime = null;

    #[ListColumn]
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '腾讯云原始响应'])]
    private ?array $rawResponse = null;

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

    public function getSendTime(): ?\DateTimeInterface
    {
        return $this->sendTime;
    }

    public function setSendTime(?\DateTimeInterface $sendTime): static
    {
        $this->sendTime = $sendTime;
        return $this;
    }

    public function getReceiveTime(): ?\DateTimeInterface
    {
        return $this->receiveTime;
    }

    public function setReceiveTime(?\DateTimeInterface $receiveTime): static
    {
        $this->receiveTime = $receiveTime;
        return $this;
    }

    public function getStatusTime(): ?\DateTimeInterface
    {
        return $this->statusTime;
    }

    public function setStatusTime(?\DateTimeInterface $statusTime): static
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
