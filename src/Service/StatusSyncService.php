<?php

namespace TencentCloudSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\DescribeSignListStatus;
use TencentCloud\Sms\V20210111\Models\DescribeSmsSignListRequest;
use TencentCloud\Sms\V20210111\Models\DescribeSmsTemplateListRequest;
use TencentCloud\Sms\V20210111\Models\DescribeTemplateListStatus;
use TencentCloudSmsBundle\Enum\SignReviewStatus;
use TencentCloudSmsBundle\Enum\TemplateReviewStatus;
use TencentCloudSmsBundle\Repository\SmsSignatureRepository;
use TencentCloudSmsBundle\Repository\SmsTemplateRepository;

class StatusSyncService
{
    public function __construct(
        private readonly SmsClient $smsClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly SmsSignatureRepository $signatureRepository,
        private readonly SmsTemplateRepository $templateRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function syncSignatures(): void
    {
        $signatures = $this->signatureRepository->findAll();

        foreach ($signatures as $signature) {
            try {
                $client = $this->smsClient->create($signature->getAccount());

                // 查询签名状态
                $req = new DescribeSmsSignListRequest();
                $params = [
                    'SignIdSet' => [$signature->getSignId()],
                    'International' => $signature->isInternational() ? 1 : 0,
                ];
                $req->fromJsonString(json_encode($params));

                $resp = $client->DescribeSmsSignList($req);
                /** @var DescribeSignListStatus[] $signStatusSet */
                $signStatusSet = $resp->getDescribeSignListStatusSet();

                if (!empty($signStatusSet)) {
                    $status = $signStatusSet[0];

                    // 标记为同步更新
                    $signature->setSyncing(true);

                    // 更新状态
                    $signature->setSignStatus(SignReviewStatus::from($status->getStatusCode()));

                    // 如果审核失败，记录原因
                    if ($status->getStatusCode() === SignReviewStatus::REJECTED->value) {
                        $signature->setReviewReply($status->getReviewReply());
                    }

                    $this->entityManager->persist($signature);

                    $this->logger->info('签名状态同步成功', [
                        'signName' => $signature->getSignName(),
                        'signId' => $signature->getSignId(),
                        'status' => $status->getStatusCode(),
                    ]);
                }
            } catch (TencentCloudSDKException $e) {
                $this->logger->error('签名状态同步失败', [
                    'signName' => $signature->getSignName(),
                    'signId' => $signature->getSignId(),
                    'error' => $e->getMessage(),
                ]);
            } finally {
                // 恢复同步标记
                $signature->setSyncing(false);
            }
        }

        $this->entityManager->flush();
    }

    public function syncTemplates(): void
    {
        $templates = $this->templateRepository->findAll();

        foreach ($templates as $template) {
            try {
                $client = $this->smsClient->create($template->getAccount());

                // 查询模板状态
                $req = new DescribeSmsTemplateListRequest();
                $params = [
                    'TemplateIdSet' => [$template->getTemplateId()],
                    'International' => $template->isInternational() ? 1 : 0,
                ];
                $req->fromJsonString(json_encode($params));

                $resp = $client->DescribeSmsTemplateList($req);
                /** @var DescribeTemplateListStatus[] $templateStatusSet */
                $templateStatusSet = $resp->getDescribeTemplateStatusSet();

                if (!empty($templateStatusSet)) {
                    $status = $templateStatusSet[0];

                    // 标记为同步更新
                    $template->setSyncing(true);

                    // 更新状态
                    $template->setTemplateStatus(TemplateReviewStatus::from($status->getStatusCode()));

                    // 如果审核失败，记录原因
                    if ($status->getStatusCode() === TemplateReviewStatus::REJECTED->value) {
                        $template->setReviewReply($status->getReviewReply());
                    }

                    $this->entityManager->persist($template);

                    $this->logger->info('模板状态同步成功', [
                        'templateName' => $template->getTemplateName(),
                        'templateId' => $template->getTemplateId(),
                        'status' => $status->getStatusCode(),
                    ]);
                }
            } catch (TencentCloudSDKException $e) {
                $this->logger->error('模板状态同步失败', [
                    'templateName' => $template->getTemplateName(),
                    'templateId' => $template->getTemplateId(),
                    'error' => $e->getMessage(),
                ]);
            } finally {
                // 恢复同步标记
                $template->setSyncing(false);
            }
        }

        $this->entityManager->flush();
    }
}
