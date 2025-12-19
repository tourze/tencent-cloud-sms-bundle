<?php

namespace TencentCloudSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\DescribePhoneNumberInfoRequest;
use TencentCloud\Sms\V20210111\Models\PhoneNumberInfo as TencentPhoneNumberInfo;
use TencentCloud\Sms\V20210111\SmsClient as TencentSmsClient;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Exception\SignatureException;
use TencentCloudSmsBundle\Repository\PhoneNumberInfoRepository;

#[WithMonologChannel(channel: 'tencent_cloud_sms')]
final class PhoneNumberInfoService
{
    private const BATCH_SIZE = 200;

    public function __construct(
        private readonly SmsClient $smsClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly PhoneNumberInfoRepository $phoneNumberInfoRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function syncPhoneNumberInfo(Account $account): void
    {
        $phoneNumbers = $this->phoneNumberInfoRepository->findBy(['nationCode' => null]);
        if ([] === $phoneNumbers) {
            return;
        }

        foreach (array_chunk($phoneNumbers, self::BATCH_SIZE) as $batch) {
            $this->processBatch($account, $batch);
        }
    }

    /**
     * @param array<PhoneNumberInfo> $batch
     */
    private function processBatch(Account $account, array $batch): void
    {
        try {
            $client = $this->smsClient->create($account);
            $phoneNumberInfoSet = $this->queryPhoneNumberInfo($client, $batch);
            $this->updatePhoneNumbers($batch, $phoneNumberInfoSet);
        } catch (TencentCloudSDKException $e) {
            $this->logger->error('手机号码信息同步失败', ['error' => $e->getMessage()]);
        } finally {
            $this->resetSyncingFlag($batch);
        }

        $this->entityManager->flush();
    }

    /**
     * @param array<PhoneNumberInfo> $batch
     * @return array<TencentPhoneNumberInfo>
     */
    private function queryPhoneNumberInfo(TencentSmsClient $client, array $batch): array
    {
        $req = new DescribePhoneNumberInfoRequest();
        $params = [
            'PhoneNumberSet' => array_map(
                fn (PhoneNumberInfo $info) => '+' . $info->getPhoneNumber(),
                $batch
            ),
        ];
        $jsonString = json_encode($params);
        if (false === $jsonString) {
            throw new SignatureException('JSON编码失败');
        }
        $req->fromJsonString($jsonString);

        $resp = $client->DescribePhoneNumberInfo($req);
        $infoSet = $resp->getPhoneNumberInfoSet();

        /** @var array<TencentPhoneNumberInfo> */
        return is_array($infoSet) ? $infoSet : [];
    }

    /**
     * @param array<PhoneNumberInfo> $batch
     * @param array<TencentPhoneNumberInfo> $phoneNumberInfoSet
     */
    private function updatePhoneNumbers(array $batch, array $phoneNumberInfoSet): void
    {
        $phoneNumberMap = $this->createPhoneNumberMap($batch);

        foreach ($phoneNumberInfoSet as $info) {
            $phoneNumber = $phoneNumberMap[$info->getPhoneNumber()] ?? null;
            if (null === $phoneNumber) {
                continue;
            }

            $this->updatePhoneNumberInfo($phoneNumber, $info);
        }
    }

    /**
     * @param array<PhoneNumberInfo> $batch
     * @return array<string, PhoneNumberInfo>
     */
    private function createPhoneNumberMap(array $batch): array
    {
        $phoneNumberMap = [];
        foreach ($batch as $info) {
            $phoneNumberMap['+' . $info->getPhoneNumber()] = $info;
        }

        return $phoneNumberMap;
    }

    private function updatePhoneNumberInfo(PhoneNumberInfo $phoneNumber, TencentPhoneNumberInfo $info): void
    {
        $phoneNumber->setSyncing(true);
        $phoneNumber->setSubscriberNumber($info->getSubscriberNumber());
        $phoneNumber->setMessage($info->getMessage());
        $phoneNumber->setCode($info->getCode());
        $phoneNumber->setFullNumber($info->getPhoneNumber());
        $phoneNumber->setIsoCode($info->getIsoCode());
        $phoneNumber->setIsoName($info->getIsoName());
        $phoneNumber->setNationCode($info->getNationCode());

        $this->entityManager->persist($phoneNumber);

        $this->logger->info('手机号码信息同步成功', [
            'phoneNumber' => $phoneNumber->getPhoneNumber(),
            'code' => $info->getCode(),
        ]);
    }

    /**
     * @param array<PhoneNumberInfo> $batch
     */
    private function resetSyncingFlag(array $batch): void
    {
        foreach ($batch as $phoneNumber) {
            $phoneNumber->setSyncing(false);
        }
    }
}
