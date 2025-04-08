<?php

namespace TencentCloudSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\DescribePhoneNumberInfoRequest;
use TencentCloud\Sms\V20210111\Models\PhoneNumberInfo as TencentPhoneNumberInfo;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\PhoneNumberInfo;
use TencentCloudSmsBundle\Repository\PhoneNumberInfoRepository;

class PhoneNumberInfoService
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
        // 获取需要同步的号码
        $phoneNumbers = $this->phoneNumberInfoRepository->findBy(['nationCode' => null]);
        if (empty($phoneNumbers)) {
            return;
        }

        // 按批次处理
        foreach (array_chunk($phoneNumbers, self::BATCH_SIZE) as $batch) {
            try {
                $client = $this->smsClient->create($account);

                // 查询号码信息
                $req = new DescribePhoneNumberInfoRequest();
                $params = [
                    'PhoneNumberSet' => array_map(
                        fn(PhoneNumberInfo $info) => '+' . $info->getPhoneNumber(),
                        $batch
                    ),
                ];
                $req->fromJsonString(json_encode($params));

                $resp = $client->DescribePhoneNumberInfo($req);
                /** @var TencentPhoneNumberInfo[] $phoneNumberInfoSet */
                $phoneNumberInfoSet = $resp->getPhoneNumberInfoSet();

                // 建立号码映射，方便更新
                $phoneNumberMap = [];
                foreach ($batch as $info) {
                    $phoneNumberMap['+' . $info->getPhoneNumber()] = $info;
                }

                foreach ($phoneNumberInfoSet as $info) {
                    $phoneNumber = $phoneNumberMap[$info->getPhoneNumber()] ?? null;
                    if (!$phoneNumber) {
                        continue;
                    }

                    // 标记为同步更新
                    $phoneNumber->setSyncing(true);

                    // 更新信息
                    $phoneNumber
                        ->setSubscriberNumber($info->getSubscriberNumber())
                        ->setMessage($info->getMessage())
                        ->setCode($info->getCode())
                        ->setFullNumber($info->getPhoneNumber())
                        ->setIsoCode($info->getIsoCode())
                        ->setIsoName($info->getIsoName())
                        ->setNationCode($info->getNationCode());

                    $this->entityManager->persist($phoneNumber);

                    $this->logger->info('手机号码信息同步成功', [
                        'phoneNumber' => $phoneNumber->getPhoneNumber(),
                        'code' => $info->getCode(),
                    ]);
                }
            } catch (TencentCloudSDKException $e) {
                $this->logger->error('手机号码信息同步失败', [
                    'error' => $e->getMessage(),
                ]);
            } finally {
                // 恢复同步标记
                foreach ($batch as $phoneNumber) {
                    $phoneNumber->setSyncing(false);
                }
            }

            $this->entityManager->flush();
        }
    }
}
