<?php

namespace TencentCloudSmsBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TencentCloudSmsBundle\Service\StatusSyncService;

#[AsCommand(
    name: self::NAME,
    description: '同步短信签名状态',
)]
class SyncSignStatusCommand extends Command
{
    public const NAME = 'tencent-cloud:sms:sync:sign-status';
    public function __construct(
        private readonly StatusSyncService $statusSyncService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->statusSyncService->syncSignatures();

        $output->writeln('短信签名状态同步完成');

        return Command::SUCCESS;
    }
}
