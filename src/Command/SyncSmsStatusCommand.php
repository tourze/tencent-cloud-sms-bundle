<?php

namespace TencentCloudSmsBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TencentCloudSmsBundle\Service\SmsStatusService;

#[AsCommand(
    name: self::NAME,
    description: '同步短信发送状态',
)]
final class SyncSmsStatusCommand extends Command
{
    public const NAME = 'tencent-cloud:sms:sync:status';

    public function __construct(
        private readonly SmsStatusService $smsStatusService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->smsStatusService->syncStatus();
            $output->writeln('短信状态同步完成');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln(sprintf('短信状态同步失败: %s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}
