<?php

namespace TencentCloudSmsBundle\Entity\Embed;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CallbackStatistics
{
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '回执量', 'default' => 0])]
    private int $callbackCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '回执成功量', 'default' => 0])]
    private int $callbackSuccessCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '回执失败量', 'default' => 0])]
    private int $callbackFailCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '内部错误量', 'default' => 0])]
    private int $internalErrorCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '无效号码量', 'default' => 0])]
    private int $invalidNumberCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '关机量', 'default' => 0])]
    private int $shutdownErrorCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '黑名单量', 'default' => 0])]
    private int $blackListCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '频率限制量', 'default' => 0])]
    private int $frequencyLimitCount = 0;

    public function getCallbackCount(): int
    {
        return $this->callbackCount;
    }

    public function setCallbackCount(int $callbackCount): self
    {
        $this->callbackCount = $callbackCount;
        return $this;
    }

    public function getCallbackSuccessCount(): int
    {
        return $this->callbackSuccessCount;
    }

    public function setCallbackSuccessCount(int $callbackSuccessCount): self
    {
        $this->callbackSuccessCount = $callbackSuccessCount;
        return $this;
    }

    public function getCallbackFailCount(): int
    {
        return $this->callbackFailCount;
    }

    public function setCallbackFailCount(int $callbackFailCount): self
    {
        $this->callbackFailCount = $callbackFailCount;
        return $this;
    }

    public function getInternalErrorCount(): int
    {
        return $this->internalErrorCount;
    }

    public function setInternalErrorCount(int $internalErrorCount): self
    {
        $this->internalErrorCount = $internalErrorCount;
        return $this;
    }

    public function getInvalidNumberCount(): int
    {
        return $this->invalidNumberCount;
    }

    public function setInvalidNumberCount(int $invalidNumberCount): self
    {
        $this->invalidNumberCount = $invalidNumberCount;
        return $this;
    }

    public function getShutdownErrorCount(): int
    {
        return $this->shutdownErrorCount;
    }

    public function setShutdownErrorCount(int $shutdownErrorCount): self
    {
        $this->shutdownErrorCount = $shutdownErrorCount;
        return $this;
    }

    public function getBlackListCount(): int
    {
        return $this->blackListCount;
    }

    public function setBlackListCount(int $blackListCount): self
    {
        $this->blackListCount = $blackListCount;
        return $this;
    }

    public function getFrequencyLimitCount(): int
    {
        return $this->frequencyLimitCount;
    }

    public function setFrequencyLimitCount(int $frequencyLimitCount): self
    {
        $this->frequencyLimitCount = $frequencyLimitCount;
        return $this;
    }
}
