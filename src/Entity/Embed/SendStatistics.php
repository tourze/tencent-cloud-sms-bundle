<?php

namespace TencentCloudSmsBundle\Entity\Embed;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class SendStatistics
{
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '短信请求量', 'default' => 0])]
    private int $requestCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '短信成功量', 'default' => 0])]
    private int $requestSuccessCount = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '短信失败量', 'default' => 0])]
    private int $requestFailCount = 0;

    public function getRequestCount(): int
    {
        return $this->requestCount;
    }

    public function setRequestCount(int $requestCount): self
    {
        $this->requestCount = $requestCount;
        return $this;
    }

    public function getRequestSuccessCount(): int
    {
        return $this->requestSuccessCount;
    }

    public function setRequestSuccessCount(int $requestSuccessCount): self
    {
        $this->requestSuccessCount = $requestSuccessCount;
        return $this;
    }

    public function getRequestFailCount(): int
    {
        return $this->requestFailCount;
    }

    public function setRequestFailCount(int $requestFailCount): self
    {
        $this->requestFailCount = $requestFailCount;
        return $this;
    }
}
