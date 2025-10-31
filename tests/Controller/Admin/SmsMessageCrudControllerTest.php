<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Controller\Admin\SmsMessageCrudController;
use TencentCloudSmsBundle\Entity\SmsMessage;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * SmsMessageCrudController 测试
 *
 * @internal
 */
#[CoversClass(SmsMessageCrudController::class)]
#[RunTestsInSeparateProcesses]
final class SmsMessageCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(SmsMessageCrudController::class);
        $this->assertInstanceOf(SmsMessageCrudController::class, $controller);
    }

    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertSame(
            SmsMessage::class,
            SmsMessageCrudController::getEntityFqcn()
        );
    }

    public function testCrudConfigurationIsValid(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(SmsMessageCrudController::class);

        // 验证配置方法返回正确的类型
        $fields = iterator_to_array($controller->configureFields('index'));
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);

        $crud = $controller->configureCrud(Crud::new());
        $this->assertInstanceOf(Crud::class, $crud);

        $filters = $controller->configureFilters(Filters::new());
        $this->assertInstanceOf(Filters::class, $filters);
    }

    protected function getControllerService(): SmsMessageCrudController
    {
        return self::getService(SmsMessageCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '腾讯云账号列' => ['腾讯云账号'];
        yield '批次号列' => ['批次号'];
        yield '短信签名列' => ['短信签名'];
        yield '短信模板ID列' => ['短信模板ID'];
        yield '发送状态列' => ['发送状态'];
        yield '发送时间列' => ['发送时间'];
        yield '接收者列表列' => ['接收者列表'];
        yield '创建时间列' => ['创建时间'];
        yield '更新时间列' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '腾讯云账号字段' => ['account'];
        yield '批次号字段' => ['batchId'];
        yield '短信签名字段' => ['signature'];
        yield '短信模板ID字段' => ['template'];
        yield '发送状态字段' => ['status'];
        yield '发送时间字段' => ['sendTime'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield '腾讯云账号字段' => ['account'];
        yield '批次号字段' => ['batchId'];
        yield '短信签名字段' => ['signature'];
        yield '短信模板ID字段' => ['template'];
        yield '发送状态字段' => ['status'];
        yield '发送时间字段' => ['sendTime'];
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
