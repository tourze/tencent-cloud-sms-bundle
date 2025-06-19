<?php

namespace TencentCloudSmsBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TencentCloudSmsBundle\Service\StatusSyncService;

#[AsCommand(
    name: self::NAME,
    description: '同步短信模板状态',
)]
class SyncTemplateStatusCommand extends Command
{
    public const NAME = 'tencent-cloud:sms:sync:template-status';
    public function __construct(
        private readonly StatusSyncService $statusSyncService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->statusSyncService->syncTemplates();

        $output->writeln('短信模板状态同步完成');

        return Command::SUCCESS;
    }
}
