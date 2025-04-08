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
    name: 'tencent-cloud:sms:sync-statistics',
    description: '同步腾讯云短信统计数据',
)]
class SyncStatisticsCommand extends Command
{
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
            ->addOption('account-id', null, InputOption::VALUE_REQUIRED, '指定同步的账号ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $startTime = new \DateTimeImmutable($input->getOption('start-time') ?? 'now -1 day');
        $endTime = new \DateTimeImmutable($input->getOption('end-time') ?? 'now');
        $accountId = $input->getOption('account-id');

        $accounts = $accountId
            ? [$this->entityManager->getReference(Account::class, $accountId)]
            : $this->accountRepository->findBy(['isEnabled' => true]);

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
