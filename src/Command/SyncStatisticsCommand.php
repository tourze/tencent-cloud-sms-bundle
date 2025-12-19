<?php

namespace TencentCloudSmsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TencentCloudSmsBundle\Entity\Account;
use TencentCloudSmsBundle\Repository\AccountRepository;
use TencentCloudSmsBundle\Service\StatisticsSyncService;

#[AsCommand(
    name: self::NAME,
    description: '同步腾讯云短信统计数据',
)]
final class SyncStatisticsCommand extends Command
{
    public const NAME = 'tencent-cloud:sms:sync-statistics';

    public function __construct(
        private readonly StatisticsSyncService $syncService,
        private readonly AccountRepository $accountRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('start-time', null, InputOption::VALUE_REQUIRED, '开始时间 (Y-m-d H:i:s)')
            ->addOption('end-time', null, InputOption::VALUE_REQUIRED, '结束时间 (Y-m-d H:i:s)')
            ->addOption('account-id', null, InputOption::VALUE_REQUIRED, '指定同步的账号ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $startTimeOption = $input->getOption('start-time');
        $endTimeOption = $input->getOption('end-time');

        $startTime = new \DateTimeImmutable(is_string($startTimeOption) ? $startTimeOption : 'now -1 day');
        $endTime = new \DateTimeImmutable(is_string($endTimeOption) ? $endTimeOption : 'now');
        $accountId = $input->getOption('account-id');

        if (null !== $accountId) {
            $accountIdStr = is_string($accountId) || is_int($accountId) ? (string) $accountId : '';
            $account = $this->entityManager->find(Account::class, $accountId);
            if (null === $account) {
                $io->error(sprintf('Account with ID %s not found', $accountIdStr));

                return Command::FAILURE;
            }
            $accounts = [$account];
        } else {
            $accounts = $this->accountRepository->findBy(['valid' => true]);
        }

        foreach ($accounts as $account) {
            try {
                $io->section(sprintf('Syncing statistics for account: %s', $account->getId()));
                $this->syncService->sync($startTime, $endTime, $account);
                $io->success(sprintf('Successfully synced statistics for account: %s', $account->getId()));
            } catch (\Throwable $e) {
                $io->error(sprintf('Failed to sync statistics for account %s: %s', $account->getId(), $e->getMessage()));

                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
