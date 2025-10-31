<?php

namespace TencentCloudSmsBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use TencentCloudSmsBundle\Exception\JsonEncodingException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(JsonEncodingException::class)]
final class JsonEncodingExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new JsonEncodingException();
        $this->assertInstanceOf(JsonEncodingException::class, $exception);
    }

    public function testCanBeInstantiatedWithMessage(): void
    {
        $message = 'JSON encoding failed';
        $exception = new JsonEncodingException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testCanBeInstantiatedWithMessageAndCode(): void
    {
        $message = 'JSON encoding failed';
        $code = 3001;
        $exception = new JsonEncodingException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testCanBeInstantiatedWithPreviousException(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new JsonEncodingException('JSON error', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
