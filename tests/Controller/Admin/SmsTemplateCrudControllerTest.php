<?php

declare(strict_types=1);

namespace TencentCloudSmsBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TencentCloudSmsBundle\Controller\Admin\SmsTemplateCrudController;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * SmsTemplateCrudController 测试
 *
 * @internal
 */
#[CoversClass(SmsTemplateCrudController::class)]
#[RunTestsInSeparateProcesses]
final class SmsTemplateCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(SmsTemplateCrudController::class);
        $this->assertInstanceOf(SmsTemplateCrudController::class, $controller);
    }

    public function testEntityFqcnIsCorrect(): void
    {
        $this->assertSame(
            SmsTemplate::class,
            SmsTemplateCrudController::getEntityFqcn()
        );
    }

    public function testCrudConfigurationIsValid(): void
    {
        $client = self::createClientWithDatabase();
        $controller = self::getService(SmsTemplateCrudController::class);

        // 验证配置方法返回正确的类型
        $fields = iterator_to_array($controller->configureFields('index'));
        $this->assertIsArray($fields);
        $this->assertNotEmpty($fields);

        $crud = $controller->configureCrud(Crud::new());
        $this->assertInstanceOf(Crud::class, $crud);

        $filters = $controller->configureFilters(Filters::new());
        $this->assertInstanceOf(Filters::class, $filters);
    }

    protected function getControllerService(): SmsTemplateCrudController
    {
        return self::getService(SmsTemplateCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列' => ['ID'];
        yield '腾讯云账号列' => ['腾讯云账号'];
        yield '模板ID列' => ['模板ID'];
        yield '模板名称列' => ['模板名称'];
        yield '模板类型列' => ['模板类型'];
        yield '审核状态列' => ['审核状态'];
        yield '国际短信列' => ['国际短信'];
        yield '是否有效列' => ['是否有效'];
        yield '同步中列' => ['同步中'];
        yield '创建者列' => ['创建者'];
        yield '更新者列' => ['更新者'];
        yield '创建时间列' => ['创建时间'];
        yield '更新时间列' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield '腾讯云账号字段' => ['account'];
        yield '模板ID字段' => ['templateId'];
        yield '模板名称字段' => ['templateName'];
        yield '模板内容字段' => ['templateContent'];
        yield '模板类型字段' => ['templateType'];
        yield '审核状态字段' => ['templateStatus'];
        yield '审核回复字段' => ['reviewReply'];
        yield '国际短信字段' => ['international'];
        yield '是否有效字段' => ['valid'];
        yield '备注说明字段' => ['remark'];
    }

    public static function provideEditPageFields(): iterable
    {
        yield '腾讯云账号字段' => ['account'];
        yield '模板ID字段' => ['templateId'];
        yield '模板名称字段' => ['templateName'];
        yield '模板内容字段' => ['templateContent'];
        yield '模板类型字段' => ['templateType'];
        yield '审核状态字段' => ['templateStatus'];
        yield '审核回复字段' => ['reviewReply'];
        yield '国际短信字段' => ['international'];
        yield '是否有效字段' => ['valid'];
        yield '备注说明字段' => ['remark'];
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
