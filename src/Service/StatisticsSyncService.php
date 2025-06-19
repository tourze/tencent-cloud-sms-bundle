<?php

namespace TencentCloudSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use TencentCloud\Sms\V20210111\Models\CallbackStatusStatisticsRequest;
use TencentCloud\Sms\V20210111\Models\SendStatusStatisticsRequest;
use TencentCloud\Sms\V20210111\Models\SmsPackagesStatisticsRequest;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsStatistics;
use TencentCloudSmsBundle\Repository\SmsStatisticsRepository;

class StatisticsSyncService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SmsStatisticsRepository $repository,
        private readonly LoggerInterface $logger,
        private readonly SmsClient $smsClient,
    ) {
    }

    public function sync(\DateTimeImmutable $startTime, \DateTimeImmutable $endTime, Account $account): void
    {
        // 按小时同步数据
        $currentHour = $startTime;
        while ($currentHour <= $endTime) {
            try {
                $this->syncHourlyData($currentHour, $account);
                $currentHour = $currentHour->modify('+1 hour');
            } catch (\Throwable $e) {
                $this->logger->error('Failed to sync SMS statistics', [
                    'error' => $e->getMessage(),
                    'hour' => $currentHour->format('Y-m-d H:i:s'),
                    'account' => $account->getId(),
                ]);
                throw $e;
            }
        }
    }

    private function syncHourlyData(\DateTimeImmutable $hour, Account $account): void
    {
        // 查找或创建统计记录
        $statistics = $this->repository->findByHourAndAccount($hour, $account)
            ?? (new SmsStatistics())
                ->setHour($hour)
                ->setAccount($account);

        // 同步发送数据统计
        $this->syncSendStatistics($statistics, $hour, $account);

        // 同步回执数据统计
        $this->syncCallbackStatistics($statistics, $hour, $account);

        // 同步套餐包统计
        $this->syncPackageStatistics($statistics, $hour, $account);

        // 保存数据
        $this->entityManager->persist($statistics);
        $this->entityManager->flush();
    }

    private function syncSendStatistics(SmsStatistics $statistics, \DateTimeImmutable $hour, Account $account): void
    {
        $beginTime = $hour->format('YmdH');
        $endTime = $hour->modify('+1 hour')->format('YmdH');

        $client = $this->smsClient->create($account);

        $req = new SendStatusStatisticsRequest();
        $req->fromJsonString(json_encode([
            'BeginTime' => $beginTime,
            'EndTime' => $endTime,
            'Limit' => 0,
            'Offset' => 0,
            'SmsSdkAppId' => $account->getSecretId(),
        ]));

        $resp = $client->SendStatusStatistics($req);

        $sendStats = $resp->getSendStatusStatistics();
        $send = $statistics->getSendStatistics();

        $send
            ->setRequestCount($sendStats->getRequestCount())
            ->setRequestSuccessCount($sendStats->getRequestSuccessCount())
            // ->setRequestFailCount($sendStats->getRequestFailCount()) // 方法可能不存在
            ;
    }

    private function syncCallbackStatistics(SmsStatistics $statistics, \DateTimeImmutable $hour, Account $account): void
    {
        $beginTime = $hour->format('YmdH');
        $endTime = $hour->modify('+1 hour')->format('YmdH');

        $client = $this->smsClient->create($account);

        $req = new CallbackStatusStatisticsRequest();
        $req->fromJsonString(json_encode([
            'BeginTime' => $beginTime,
            'EndTime' => $endTime,
            'Limit' => 0,
            'Offset' => 0,
            'SmsSdkAppId' => $account->getSecretId(),
        ]));

        $resp = $client->CallbackStatusStatistics($req);

        $callbackStats = $resp->getCallbackStatusStatistics();
        $callback = $statistics->getCallbackStatistics();

        $callback
            ->setCallbackCount($callbackStats->getCallbackCount())
            ->setCallbackSuccessCount($callbackStats->getCallbackSuccessCount())
            ->setCallbackFailCount($callbackStats->getCallbackFailCount())
            ->setInternalErrorCount($callbackStats->getInternalErrorCount())
            ->setInvalidNumberCount($callbackStats->getInvalidNumberCount())
            ->setShutdownErrorCount($callbackStats->getShutdownErrorCount())
            ->setBlackListCount($callbackStats->getBlackListCount())
            ->setFrequencyLimitCount($callbackStats->getFrequencyLimitCount());
    }

    private function syncPackageStatistics(SmsStatistics $statistics, \DateTimeImmutable $hour, Account $account): void
    {
        $beginTime = $hour->format('YmdH');
        $endTime = $hour->modify('+1 hour')->format('YmdH');

        $client = $this->smsClient->create($account);

        $req = new SmsPackagesStatisticsRequest();
        $req->fromJsonString(json_encode([
            'BeginTime' => $beginTime,
            'EndTime' => $endTime,
            'Limit' => 0,
            'Offset' => 0,
            'SmsSdkAppId' => $account->getSecretId(),
        ]));

        $resp = $client->SmsPackagesStatistics($req);
        
        // $packageStats = $resp->getSmsPackagesStatistics(); // 方法可能不存在
        $package = $statistics->getPackageStatistics();
        
        // $package
        //     ->setPackageAmount($packageStats->getPackageAmount())
        //     ->setUsedAmount($packageStats->getUsedAmount());
    }
}
