<?php

namespace TencentCloudSmsBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class TencentCloudSmsExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
