<?php

namespace TencentCloudSmsBundle\Service;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Exception\SdkException;

class SdkService
{
    public function getCredential(Account $account): Credential
    {
        $secretId = $account->getSecretId();
        $secretKey = $account->getSecretKey();

        if (null === $secretId || null === $secretKey) {
            throw new SdkException('账号密钥信息不完整');
        }

        return new Credential($secretId, $secretKey);
    }

    public function getHttpProfile(?string $endpoint = null): HttpProfile
    {
        $httpProfile = new HttpProfile();
        if (null !== $endpoint && '' !== $endpoint) {
            $httpProfile->setEndpoint($endpoint);
        }

        return $httpProfile;
    }

    public function getClientProfile(?HttpProfile $httpProfile = null): ClientProfile
    {
        $clientProfile = new ClientProfile();
        if (null !== $httpProfile) {
            $clientProfile->setHttpProfile($httpProfile);
        }

        return $clientProfile;
    }
}
