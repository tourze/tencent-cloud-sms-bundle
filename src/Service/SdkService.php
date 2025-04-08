<?php

namespace TencentCloudSmsBundle\Service;

use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloudSmsBundle\Entity\Account;

class SdkService
{
    public function getCredential(Account $account): Credential
    {
        return new Credential($account->getSecretId(), $account->getSecretKey());
    }

    public function getHttpProfile(?string $endpoint = null): HttpProfile
    {
        $httpProfile = new HttpProfile();
        if ($endpoint) {
            $httpProfile->setEndpoint($endpoint);
        }
        return $httpProfile;
    }

    public function getClientProfile(?HttpProfile $httpProfile = null): ClientProfile
    {
        $clientProfile = new ClientProfile();
        if ($httpProfile) {
            $clientProfile->setHttpProfile($httpProfile);
        }
        return $clientProfile;
    }
}
