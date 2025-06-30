<?php

namespace TencentCloudSmsBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Exception\SignatureException;

class SignatureExceptionTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new SignatureException();
        $this->assertInstanceOf(SignatureException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
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
