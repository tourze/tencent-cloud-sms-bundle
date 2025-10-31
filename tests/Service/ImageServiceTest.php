<?php

namespace TencentCloudSmsBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Exception\SignatureException;
use TencentCloudSmsBundle\Service\ImageService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ImageService::class)]
#[RunTestsInSeparateProcesses]
final class ImageServiceTest extends AbstractIntegrationTestCase
{
    private ImageService $imageService;

    protected function onSetUp(): void
    {
        // 直接创建ImageService实例进行单元测试
        // @phpstan-ignore integrationTest.noDirectInstantiationOfCoveredClass
        $this->imageService = new ImageService();
    }

    public function testGetBase64FromUrlWithValidUrl(): void
    {
        // 创建一个简单的PNG图片数据
        $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==', true);

        // 创建临时文件
        $tempFile = tempnam(sys_get_temp_dir(), 'test_image');
        file_put_contents($tempFile, $pngData);

        try {
            $result = $this->imageService->getBase64FromUrl($tempFile);

            $this->assertIsString($result);
            $this->assertNotEmpty($result);
            // 验证是有效的base64字符串
            $this->assertNotFalse(base64_decode($result, true));
        } finally {
            unlink($tempFile);
        }
    }

    public function testGetBase64FromUrlWithInvalidUrl(): void
    {
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('无法读取图片文件：');

        $this->imageService->getBase64FromUrl('/non/existent/file.jpg');
    }

    public function testGetBase64FromUrlWithEmptyUrl(): void
    {
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('无法读取图片文件：');

        $this->imageService->getBase64FromUrl('');
    }

    #[DataProvider('provideInvalidUrls')]
    public function testGetBase64FromUrlWithInvalidUrls(string $url): void
    {
        $this->expectException(SignatureException::class);
        $this->expectExceptionMessage('无法读取图片文件：');

        $this->imageService->getBase64FromUrl($url);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidUrls(): array
    {
        return [
            'empty string' => [''],
            'non-existent file' => ['/does/not/exist.jpg'],
            'invalid protocol' => ['invalid://example.com/image.jpg'],
        ];
    }

    public function testGetBase64FromUrlWithDataUri(): void
    {
        // 创建一个简单的PNG图片数据
        $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==', true);

        // 创建临时文件
        $tempFile = tempnam(sys_get_temp_dir(), 'test_image');
        file_put_contents($tempFile, $pngData);

        try {
            $result = $this->imageService->getBase64FromUrl($tempFile);

            $this->assertIsString($result);
            $this->assertNotEmpty($result);
            // 验证结果不包含data URI前缀
            $this->assertStringNotContainsString('data:image/', $result);
            $this->assertStringNotContainsString('base64,', $result);
        } finally {
            unlink($tempFile);
        }
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ImageService::class, $this->imageService);
    }
}
