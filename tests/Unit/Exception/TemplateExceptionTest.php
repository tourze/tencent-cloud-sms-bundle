<?php

namespace TencentCloudSmsBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use TencentCloudSmsBundle\Exception\TemplateException;

class TemplateExceptionTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new TemplateException();
        $this->assertInstanceOf(TemplateException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testCanBeInstantiatedWithMessage(): void
    {
        $message = 'Test template exception';
        $exception = new TemplateException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testCanBeInstantiatedWithMessageAndCode(): void
    {
        $message = 'Test template exception';
        $code = 500;
        $exception = new TemplateException($message, $code);
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testCanBeInstantiatedWithPreviousException(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new TemplateException('Test message', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }
}
