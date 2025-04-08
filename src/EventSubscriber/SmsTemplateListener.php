<?php

namespace TencentCloudSmsBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\AddSmsTemplateRequest;
use TencentCloud\Sms\V20210111\Models\DeleteSmsTemplateRequest;
use TencentCloud\Sms\V20210111\Models\ModifySmsTemplateRequest;
use TencentCloudSmsBundle\Entity\SmsTemplate;
use TencentCloudSmsBundle\Exception\TemplateException;
use TencentCloudSmsBundle\Service\SmsClient;

#[AsEntityListener(event: Events::prePersist, method: 'createRemoteTemplate', entity: SmsTemplate::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'updateRemoteTemplate', entity: SmsTemplate::class)]
#[AsEntityListener(event: Events::preRemove, method: 'deleteRemoteTemplate', entity: SmsTemplate::class)]
class SmsTemplateListener
{
    public function __construct(
        private readonly SmsClient $smsClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws TemplateException
     */
    public function createRemoteTemplate(SmsTemplate $template): void
    {
        // 如果是同步更新，不调用远程接口
        if ($template->isSyncing()) {
            return;
        }

        try {
            // 实例化 SMS 的 client 对象
            $client = $this->smsClient->create($template->getAccount());

            // 实例化一个请求对象
            $req = new AddSmsTemplateRequest();
            $params = [
                "TemplateName" => $template->getTemplateName(),
                "TemplateContent" => $template->getTemplateContent(),
                "SmsType" => $template->getTemplateType()->value,
                "International" => $template->isInternational() ? 1 : 0,
                "Remark" => $template->getRemark(),
            ];
            $req->fromJsonString(json_encode($params));

            // 发起添加模板请求
            $resp = $client->AddSmsTemplate($req);

            // 保存返回的 TemplateId
            $template->setTemplateId($resp->getAddTemplateStatus()->getTemplateId());

            $this->logger->info('短信模板创建成功', [
                'templateName' => $template->getTemplateName(),
                'templateId' => $template->getTemplateId(),
            ]);
        } catch (TencentCloudSDKException $e) {
            $this->logger->error('短信模板创建失败', [
                'templateName' => $template->getTemplateName(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw new TemplateException(
                sprintf('创建模板失败：%s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws TemplateException
     */
    public function updateRemoteTemplate(SmsTemplate $template): void
    {
        // 如果是同步更新，不调用远程接口
        if ($template->isSyncing()) {
            return;
        }

        try {
            // 实例化 SMS 的 client 对象
            $client = $this->smsClient->create($template->getAccount());

            // 实例化一个请求对象
            $req = new ModifySmsTemplateRequest();
            $params = [
                "TemplateId" => $template->getTemplateId(),
                "TemplateName" => $template->getTemplateName(),
                "TemplateContent" => $template->getTemplateContent(),
                "SmsType" => $template->getTemplateType()->value,
                "International" => $template->isInternational() ? 1 : 0,
                "Remark" => $template->getRemark(),
            ];
            $req->fromJsonString(json_encode($params));

            // 发起修改模板请求
            $client->ModifySmsTemplate($req);

            $this->logger->info('短信模板更新成功', [
                'templateName' => $template->getTemplateName(),
                'templateId' => $template->getTemplateId(),
            ]);
        } catch (TencentCloudSDKException $e) {
            $this->logger->error('短信模板更新失败', [
                'templateName' => $template->getTemplateName(),
                'templateId' => $template->getTemplateId(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw new TemplateException(
                sprintf('更新模板失败：%s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws TemplateException
     */
    public function deleteRemoteTemplate(SmsTemplate $template): void
    {
        // 如果是同步更新，不调用远程接口
        if ($template->isSyncing()) {
            return;
        }

        try {
            // 实例化 SMS 的 client 对象
            $client = $this->smsClient->create($template->getAccount());

            // 实例化一个请求对象
            $req = new DeleteSmsTemplateRequest();
            $params = [
                "TemplateId" => $template->getTemplateId(),
            ];
            $req->fromJsonString(json_encode($params));

            // 发起删除模板请求
            $client->DeleteSmsTemplate($req);

            $this->logger->info('短信模板删除成功', [
                'templateName' => $template->getTemplateName(),
                'templateId' => $template->getTemplateId(),
            ]);
        } catch (TencentCloudSDKException $e) {
            $this->logger->error('短信模板删除失败', [
                'templateName' => $template->getTemplateName(),
                'templateId' => $template->getTemplateId(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw new TemplateException(
                sprintf('删除模板失败：%s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }
}
