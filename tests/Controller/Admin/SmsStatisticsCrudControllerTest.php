<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Controller\Admin\SmsStatisticsCrudController;
use TencentCloudSmsBundle\Entity\SmsStatistics;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * SmsStatisticsCrudController 测试
 *
 * @internal
 */
#[CoversClass(SmsStatisticsCrudController::class)]
#[RunTestsInSeparateProcesses]
final class SmsStatisticsCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(SmsStatisticsCrudController::class);
        $this->assertInstanceOf(SmsStatisticsCrudController::class, $controller);
    }

    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertSame(
            SmsStatistics::class,
            SmsStatisticsCrudController::getEntityFqcn()
        );
    }

    public function testCrudConfigurationIsValid(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(SmsStatisticsCrudController::class);

        // 验证配置方法返回正确的类型
        $fields = iterator_to_array($controller->configureFields('index'));
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);

        $crud = $controller->configureCrud(Crud::new());
        $this->assertInstanceOf(Crud::class, $crud);

        $filters = $controller->configureFilters(Filters::new());
        $this->assertInstanceOf(Filters::class, $filters);
    }

    protected function getControllerService(): SmsStatisticsCrudController
    {
        return self::getService(SmsStatisticsCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '腾讯云账号列' => ['腾讯云账号'];
        yield '统计小时列' => ['统计小时'];
        yield '短信请求量列' => ['短信请求量'];
        yield '短信成功量列' => ['短信成功量'];
        yield '短信失败量列' => ['短信失败量'];
        yield '套餐包条数列' => ['套餐包条数'];
        yield '套餐包已用条数列' => ['套餐包已用条数'];
        yield '创建时间列' => ['创建时间'];
        yield '更新时间列' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '腾讯云账号字段' => ['account'];
        yield '统计小时字段' => ['hour'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield '腾讯云账号字段' => ['account'];
        yield '统计小时字段' => ['hour'];
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
