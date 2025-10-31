<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Controller\Admin\PhoneNumberInfoCrudController;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * PhoneNumberInfoCrudController 测试
 *
 * @internal
 */
#[CoversClass(PhoneNumberInfoCrudController::class)]
#[RunTestsInSeparateProcesses]
final class PhoneNumberInfoCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(PhoneNumberInfoCrudController::class);
        $this->assertInstanceOf(PhoneNumberInfoCrudController::class, $controller);
    }

    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertSame(
            PhoneNumberInfo::class,
            PhoneNumberInfoCrudController::getEntityFqcn()
        );
    }

    public function testCrudConfigurationIsValid(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(PhoneNumberInfoCrudController::class);

        // 验证配置方法返回正确的类型
        $fields = iterator_to_array($controller->configureFields('index'));
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);

        $crud = $controller->configureCrud(Crud::new());
        $this->assertInstanceOf(Crud::class, $crud);

        $filters = $controller->configureFilters(Filters::new());
        $this->assertInstanceOf(Filters::class, $filters);
    }

    protected function getControllerService(): PhoneNumberInfoCrudController
    {
        return self::getService(PhoneNumberInfoCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '手机号码列' => ['手机号码'];
        yield '国家码列' => ['国家码'];
        yield 'ISO编码列' => ['ISO编码'];
        yield '国家/地区名称列' => ['国家/地区名称'];
        yield '用户号码列' => ['用户号码'];
        yield '完整号码列' => ['完整号码'];
        yield '状态码列' => ['状态码'];
        yield '正在同步列' => ['正在同步'];
        yield '创建时间列' => ['创建时间'];
        yield '更新时间列' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '手机号码字段' => ['phoneNumber'];
        yield '国家码字段' => ['nationCode'];
        yield 'ISO编码字段' => ['isoCode'];
        yield '国家/地区名称字段' => ['isoName'];
        yield '用户号码字段' => ['subscriberNumber'];
        yield '完整号码字段' => ['fullNumber'];
        yield '状态码字段' => ['code'];
        yield '查询结果字段' => ['message'];
        yield '正在同步字段' => ['syncing'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield '手机号码字段' => ['phoneNumber'];
        yield '国家码字段' => ['nationCode'];
        yield 'ISO编码字段' => ['isoCode'];
        yield '国家/地区名称字段' => ['isoName'];
        yield '用户号码字段' => ['subscriberNumber'];
        yield '完整号码字段' => ['fullNumber'];
        yield '状态码字段' => ['code'];
        yield '查询结果字段' => ['message'];
        yield '正在同步字段' => ['syncing'];
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
