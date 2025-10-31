<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\TencentCloudSmsBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(TencentCloudSmsBundle::class)]
#[RunTestsInSeparateProcesses]
final class TencentCloudSmsBundleTest extends AbstractBundleTestCase
{
}
