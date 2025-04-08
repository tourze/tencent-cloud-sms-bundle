<?php

namespace TencentCloudSmsBundle\Service;

use TencentCloud\Sms\V20210111\SmsClient as TencentSmsClient;
use TencentCloudSmsBundle\Entity\Account;

class SmsClient
{
    private const ENDPOINT = "sms.tencentcloudapi.com";

    public function __construct(
        private readonly SdkService $sdkService,
    ) {
    }

    public function create(Account $account): TencentSmsClient
    {
        // 创建认证对象
        $cred = $this->sdkService->getCredential($account);

        // 实例化 SMS 的 client 对象
        return new TencentSmsClient(
            $cred,
            "",
            $this->sdkService->getClientProfile(
                $this->sdkService->getHttpProfile(self::ENDPOINT)
            )
        );
    }
}
