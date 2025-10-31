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

    public function setCallbackCount(int $callbackCount): void
    {
        $this->callbackCount = $callbackCount;
    }

    public function getCallbackSuccessCount(): int
    {
        return $this->callbackSuccessCount;
    }

    public function setCallbackSuccessCount(int $callbackSuccessCount): void
    {
        $this->callbackSuccessCount = $callbackSuccessCount;
    }

    public function getCallbackFailCount(): int
    {
        return $this->callbackFailCount;
    }

    public function setCallbackFailCount(int $callbackFailCount): void
    {
        $this->callbackFailCount = $callbackFailCount;
    }

    public function getInternalErrorCount(): int
    {
        return $this->internalErrorCount;
    }

    public function setInternalErrorCount(int $internalErrorCount): void
    {
        $this->internalErrorCount = $internalErrorCount;
    }

    public function getInvalidNumberCount(): int
    {
        return $this->invalidNumberCount;
    }

    public function setInvalidNumberCount(int $invalidNumberCount): void
    {
        $this->invalidNumberCount = $invalidNumberCount;
    }

    public function getShutdownErrorCount(): int
    {
        return $this->shutdownErrorCount;
    }

    public function setShutdownErrorCount(int $shutdownErrorCount): void
    {
        $this->shutdownErrorCount = $shutdownErrorCount;
    }

    public function getBlackListCount(): int
    {
        return $this->blackListCount;
    }

    public function setBlackListCount(int $blackListCount): void
    {
        $this->blackListCount = $blackListCount;
    }

    public function getFrequencyLimitCount(): int
    {
        return $this->frequencyLimitCount;
    }

    public function setFrequencyLimitCount(int $frequencyLimitCount): void
    {
        $this->frequencyLimitCount = $frequencyLimitCount;
    }
}
