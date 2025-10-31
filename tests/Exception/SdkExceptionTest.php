<?php

namespace TencentCloudSmsBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Exception\SdkException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(SdkException::class)]
final class SdkExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new SdkException();
        $this->assertInstanceOf(SdkException::class, $exception);
    }

    public function testCanBeInstantiatedWithMessage(): void
    {
        $message = 'SDK configuration error';
        $exception = new SdkException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testCanBeInstantiatedWithMessageAndCode(): void
    {
        $message = 'SDK configuration error';
        $code = 1001;
        $exception = new SdkException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testCanBeInstantiatedWithPreviousException(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new SdkException('SDK error', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
