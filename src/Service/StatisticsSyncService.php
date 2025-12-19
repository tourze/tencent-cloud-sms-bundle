<?php

namespace TencentCloudSmsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use TencentCloud\Sms\V20210111\Models\CallbackStatusStatisticsRequest;
use TencentCloud\Sms\V20210111\Models\SendStatusStatisticsRequest;
use TencentCloud\Sms\V20210111\Models\SmsPackagesStatisticsRequest;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Entity\SmsStatistics;
use TencentCloudSmsBundle\Exception\JsonEncodingException;
use TencentCloudSmsBundle\Repository\SmsStatisticsRepository;

#[WithMonologChannel(channel: 'tencent_cloud_sms')]
final class StatisticsSyncService
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
        $statistics = $this->repository->findByHourAndAccount($hour, $account);
        if (null === $statistics) {
            $statistics = new SmsStatistics();
            $statistics->setHour($hour);
            $statistics->setAccount($account);
        }

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
        $jsonString = json_encode([
            'BeginTime' => $beginTime,
            'EndTime' => $endTime,
            'Limit' => 0,
            'Offset' => 0,
            'SmsSdkAppId' => $account->getSecretId(),
        ]);
        if (false === $jsonString) {
            throw new JsonEncodingException('JSON编码失败');
        }
        $req->fromJsonString($jsonString);

        $resp = $client->SendStatusStatistics($req);

        $sendStats = $resp->getSendStatusStatistics();
        $send = $statistics->getSendStatistics();

        $send->setRequestCount($sendStats->getRequestCount());
        $send->setRequestSuccessCount($sendStats->getRequestSuccessCount());
        // $send->setRequestFailCount($sendStats->getRequestFailCount()); // 方法可能不存在
    }

    private function syncCallbackStatistics(SmsStatistics $statistics, \DateTimeImmutable $hour, Account $account): void
    {
        $beginTime = $hour->format('YmdH');
        $endTime = $hour->modify('+1 hour')->format('YmdH');

        $client = $this->smsClient->create($account);

        $req = new CallbackStatusStatisticsRequest();
        $jsonString = json_encode([
            'BeginTime' => $beginTime,
            'EndTime' => $endTime,
            'Limit' => 0,
            'Offset' => 0,
            'SmsSdkAppId' => $account->getSecretId(),
        ]);
        if (false === $jsonString) {
            throw new JsonEncodingException('JSON编码失败');
        }
        $req->fromJsonString($jsonString);

        $resp = $client->CallbackStatusStatistics($req);

        $callbackStats = $resp->getCallbackStatusStatistics();
        $callback = $statistics->getCallbackStatistics();

        $callback->setCallbackCount($callbackStats->getCallbackCount());
        $callback->setCallbackSuccessCount($callbackStats->getCallbackSuccessCount());
        $callback->setCallbackFailCount($callbackStats->getCallbackFailCount());
        $callback->setInternalErrorCount($callbackStats->getInternalErrorCount());
        $callback->setInvalidNumberCount($callbackStats->getInvalidNumberCount());
        $callback->setShutdownErrorCount($callbackStats->getShutdownErrorCount());
        $callback->setBlackListCount($callbackStats->getBlackListCount());
        $callback->setFrequencyLimitCount($callbackStats->getFrequencyLimitCount());
    }

    private function syncPackageStatistics(SmsStatistics $statistics, \DateTimeImmutable $hour, Account $account): void
    {
        $beginTime = $hour->format('YmdH');
        $endTime = $hour->modify('+1 hour')->format('YmdH');

        $client = $this->smsClient->create($account);

        $req = new SmsPackagesStatisticsRequest();
        $jsonString = json_encode([
            'BeginTime' => $beginTime,
            'EndTime' => $endTime,
            'Limit' => 0,
            'Offset' => 0,
            'SmsSdkAppId' => $account->getSecretId(),
        ]);
        if (false === $jsonString) {
            throw new JsonEncodingException('JSON编码失败');
        }
        $req->fromJsonString($jsonString);

        $resp = $client->SmsPackagesStatistics($req);
        $packageStatsSet = $resp->getSmsPackagesStatisticsSet();

        $this->updatePackageStatistics($statistics, $packageStatsSet);
    }

    /**
     * @param mixed $packageStatsSet
     */
    private function updatePackageStatistics(SmsStatistics $statistics, $packageStatsSet): void
    {
        if (!is_array($packageStatsSet) || [] === $packageStatsSet) {
            return;
        }

        $packageStats = $packageStatsSet[0];
        if (
            !is_object($packageStats)
            || !method_exists($packageStats, 'getPackageAmount')
            || !method_exists($packageStats, 'getCurrentUsage')
        ) {
            return;
        }

        $package = $statistics->getPackageStatistics();
        $packageAmount = $packageStats->getPackageAmount();
        $currentUsage = $packageStats->getCurrentUsage();

        if (is_int($packageAmount)) {
            $package->setPackageAmount($packageAmount);
        }
        if (is_int($currentUsage)) {
            $package->setUsedAmount($currentUsage);
        }
    }
}
