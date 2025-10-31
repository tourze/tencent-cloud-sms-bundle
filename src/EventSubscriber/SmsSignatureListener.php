<?php

namespace TencentCloudSmsBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\AddSmsSignRequest;
use TencentCloud\Sms\V20210111\Models\DeleteSmsSignRequest;
use TencentCloud\Sms\V20210111\Models\ModifySmsSignRequest;
use TencentCloudSmsBundle\Entity\SmsSignature;
use TencentCloudSmsBundle\Exception\SignatureException;
use TencentCloudSmsBundle\Service\ImageService;
use TencentCloudSmsBundle\Service\SmsClient;

#[AsEntityListener(event: Events::prePersist, method: 'createRemoteSignature', entity: SmsSignature::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'updateRemoteSignature', entity: SmsSignature::class)]
#[AsEntityListener(event: Events::preRemove, method: 'deleteRemoteSignature', entity: SmsSignature::class)]
#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'tencent_cloud_sms')]
class SmsSignatureListener
{
    public function __construct(
        private readonly SmsClient $smsClient,
        private readonly ImageService $imageService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws SignatureException
     */
    public function createRemoteSignature(SmsSignature $signature): void
    {
        // 如果是同步更新，不调用远程接口
        if ($signature->isSyncing()) {
            return;
        }

        $account = $signature->getAccount();
        if (null === $account) {
            throw new SignatureException('签名账号不能为空');
        }

        $signType = $signature->getSignType();
        if (null === $signType) {
            throw new SignatureException('签名类型不能为空');
        }

        $documentType = $signature->getDocumentType();
        if (null === $documentType) {
            throw new SignatureException('证明类型不能为空');
        }

        $signPurpose = $signature->getSignPurpose();
        if (null === $signPurpose) {
            throw new SignatureException('签名用途不能为空');
        }

        $documentUrl = $signature->getDocumentUrl();
        if (null === $documentUrl) {
            throw new SignatureException('证明文件URL不能为空');
        }

        try {
            // 实例化 SMS 的 client 对象
            $client = $this->smsClient->create($account);

            // 实例化一个请求对象
            $req = new AddSmsSignRequest();
            $params = [
                'SignName' => $signature->getSignName(),
                'SignType' => $signType->value,
                'DocumentType' => $documentType->value,
                'International' => $signature->isInternational() ? 1 : 0,
                'SignPurpose' => $signPurpose->value,
                'ProofImage' => $this->imageService->getBase64FromUrl($documentUrl),
                'Remark' => $signature->getSignContent(),
            ];
            $jsonString = json_encode($params);
            if (false === $jsonString) {
                throw new SignatureException('JSON编码失败');
            }
            $req->fromJsonString($jsonString);

            // 发起添加签名请求
            $resp = $client->AddSmsSign($req);

            // 保存返回的 SignId
            $signature->setSignId((string) $resp->getAddSignStatus()->getSignId());

            $this->logger->info('短信签名创建成功', [
                'signName' => $signature->getSignName(),
                'signId' => $signature->getSignId(),
            ]);
        } catch (TencentCloudSDKException $e) {
            $this->logger->error('短信签名创建失败', [
                'signName' => $signature->getSignName(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw new SignatureException(sprintf('创建签名失败：%s', $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @throws SignatureException
     */
    public function updateRemoteSignature(SmsSignature $signature): void
    {
        // 如果是同步更新，不调用远程接口
        if ($signature->isSyncing()) {
            return;
        }

        $account = $signature->getAccount();
        if (null === $account) {
            throw new SignatureException('签名账号不能为空');
        }

        $signType = $signature->getSignType();
        if (null === $signType) {
            throw new SignatureException('签名类型不能为空');
        }

        $documentType = $signature->getDocumentType();
        if (null === $documentType) {
            throw new SignatureException('证明类型不能为空');
        }

        $signPurpose = $signature->getSignPurpose();
        if (null === $signPurpose) {
            throw new SignatureException('签名用途不能为空');
        }

        $documentUrl = $signature->getDocumentUrl();
        if (null === $documentUrl) {
            throw new SignatureException('证明文件URL不能为空');
        }

        try {
            // 实例化 SMS 的 client 对象
            $client = $this->smsClient->create($account);

            // 实例化一个请求对象
            $req = new ModifySmsSignRequest();
            $params = [
                'SignId' => $signature->getSignId(),
                'SignName' => $signature->getSignName(),
                'SignType' => $signType->value,
                'DocumentType' => $documentType->value,
                'International' => $signature->isInternational() ? 1 : 0,
                'SignPurpose' => $signPurpose->value,
                'ProofImage' => $this->imageService->getBase64FromUrl($documentUrl),
                'Remark' => $signature->getSignContent(),
            ];
            $jsonString = json_encode($params);
            if (false === $jsonString) {
                throw new SignatureException('JSON编码失败');
            }
            $req->fromJsonString($jsonString);

            // 发起修改签名请求
            $client->ModifySmsSign($req);

            $this->logger->info('短信签名更新成功', [
                'signName' => $signature->getSignName(),
                'signId' => $signature->getSignId(),
            ]);
        } catch (TencentCloudSDKException $e) {
            $this->logger->error('短信签名更新失败', [
                'signName' => $signature->getSignName(),
                'signId' => $signature->getSignId(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw new SignatureException(sprintf('更新签名失败：%s', $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * @throws SignatureException
     */
    public function deleteRemoteSignature(SmsSignature $signature): void
    {
        // 如果是同步更新，不调用远程接口
        if ($signature->isSyncing()) {
            return;
        }

        $account = $signature->getAccount();
        if (null === $account) {
            throw new SignatureException('签名账号不能为空');
        }

        try {
            // 实例化 SMS 的 client 对象
            $client = $this->smsClient->create($account);

            // 实例化一个请求对象
            $req = new DeleteSmsSignRequest();
            $params = [
                'SignId' => $signature->getSignId(),
            ];
            $jsonString = json_encode($params);
            if (false === $jsonString) {
                throw new SignatureException('JSON编码失败');
            }
            $req->fromJsonString($jsonString);

            // 发起删除签名请求
            $client->DeleteSmsSign($req);

            $this->logger->info('短信签名删除成功', [
                'signName' => $signature->getSignName(),
                'signId' => $signature->getSignId(),
            ]);
        } catch (TencentCloudSDKException $e) {
            $this->logger->error('短信签名删除失败', [
                'signName' => $signature->getSignName(),
                'signId' => $signature->getSignId(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            throw new SignatureException(sprintf('删除签名失败：%s', $e->getMessage()), $e->getCode(), $e);
        }
    }
}
