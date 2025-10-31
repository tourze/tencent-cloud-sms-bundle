<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Controller\Admin\SmsSignatureCrudController;
use TencentCloudSmsBundle\Entity\SmsSignature;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * SmsSignatureCrudController 测试
 *
 * @internal
 */
#[CoversClass(SmsSignatureCrudController::class)]
#[RunTestsInSeparateProcesses]
final class SmsSignatureCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(SmsSignatureCrudController::class);
        $this->assertInstanceOf(SmsSignatureCrudController::class, $controller);
    }

    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertSame(
            SmsSignature::class,
            SmsSignatureCrudController::getEntityFqcn()
        );
    }

    public function testCrudConfigurationIsValid(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(SmsSignatureCrudController::class);

        // 验证配置方法返回正确的类型
        $fields = iterator_to_array($controller->configureFields('index'));
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);

        $crud = $controller->configureCrud(Crud::new());
        $this->assertInstanceOf(Crud::class, $crud);

        $filters = $controller->configureFilters(Filters::new());
        $this->assertInstanceOf(Filters::class, $filters);
    }

    protected function getControllerService(): SmsSignatureCrudController
    {
        return self::getService(SmsSignatureCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '腾讯云账号列' => ['腾讯云账号'];
        yield '签名ID列' => ['签名ID'];
        yield '签名名称列' => ['签名名称'];
        yield '签名类型列' => ['签名类型'];
        yield '证明类型列' => ['证明类型'];
        yield '签名用途列' => ['签名用途'];
        yield '国际/港澳台列' => ['国际/港澳台'];
        yield '审核状态列' => ['审核状态'];
        yield '是否有效列' => ['是否有效'];
        yield '正在同步列' => ['正在同步'];
        yield '创建时间列' => ['创建时间'];
        yield '更新时间列' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '腾讯云账号字段' => ['account'];
        yield '签名名称字段' => ['signName'];
        yield '签名类型字段' => ['signType'];
        yield '证明类型字段' => ['documentType'];
        yield '证明文件URL字段' => ['documentUrl'];
        yield '签名用途字段' => ['signPurpose'];
        yield '国际/港澳台字段' => ['international'];
        yield '审核状态字段' => ['signStatus'];
        yield '签名内容字段' => ['signContent'];
        yield '是否有效字段' => ['valid'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield '腾讯云账号字段' => ['account'];
        yield '签名名称字段' => ['signName'];
        yield '签名类型字段' => ['signType'];
        yield '证明类型字段' => ['documentType'];
        yield '证明文件URL字段' => ['documentUrl'];
        yield '签名用途字段' => ['signPurpose'];
        yield '国际/港澳台字段' => ['international'];
        yield '审核状态字段' => ['signStatus'];
        yield '签名内容字段' => ['signContent'];
        yield '是否有效字段' => ['valid'];
    }

    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        $crawler = $client->request('GET', $this->generateAdminUrl('new'));
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Create')->form();

        // 提交空表单触发验证错误
        $client->submit($form);
        $this->assertResponseStatusCodeSame(422);
    }
}
