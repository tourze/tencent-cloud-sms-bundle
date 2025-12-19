<?php

namespace TencentCloudSmsBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TencentCloudSmsBundle\Service\SmsStatusService;

#[AsCommand(
    name: self::NAME,
    description: '同步未知状态的短信记录',
)]
final class SyncUnknownStatusCommand extends Command
{
    public const NAME = 'tencent-cloud:sms:sync:unknown-status';

    public function __construct(
        private readonly SmsStatusService $smsStatusService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, '每次同步的最大记录数', 100)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $limitOption = $input->getOption('limit');
            $limit = is_numeric($limitOption) ? (int) $limitOption : 100;

            $this->smsStatusService->syncUnknownStatus($limit);
            $output->writeln('未知状态短信同步完成');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln(sprintf('未知状态短信同步失败: %s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}
