<?php

namespace TencentCloudSmsBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Exception\SmsException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(SmsException::class)]
final class SmsExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new SmsException();
        $this->assertInstanceOf(SmsException::class, $exception);
    }

    public function testCanBeInstantiatedWithMessage(): void
    {
        $message = 'SMS sending error';
        $exception = new SmsException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testCanBeInstantiatedWithMessageAndCode(): void
    {
        $message = 'SMS sending error';
        $code = 2001;
        $exception = new SmsException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testCanBeInstantiatedWithPreviousException(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new SmsException('SMS error', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
