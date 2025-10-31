<?php

namespace TencentCloudSmsBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Exception\TemplateException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(TemplateException::class)]
final class TemplateExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new TemplateException();
        $this->assertNotNull($exception);
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
