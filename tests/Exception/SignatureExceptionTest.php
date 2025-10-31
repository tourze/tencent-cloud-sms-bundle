<?php

namespace TencentCloudSmsBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Exception\SignatureException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(SignatureException::class)]
final class SignatureExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new SignatureException();
        $this->assertNotNull($exception);
    }

    public function testCanBeInstantiatedWithMessage(): void
    {
        $message = 'Test signature exception';
        $exception = new SignatureException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testCanBeInstantiatedWithMessageAndCode(): void
    {
        $message = 'Test signature exception';
        $code = 500;
        $exception = new SignatureException($message, $code);
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testCanBeInstantiatedWithPreviousException(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new SignatureException('Test message', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }
}
