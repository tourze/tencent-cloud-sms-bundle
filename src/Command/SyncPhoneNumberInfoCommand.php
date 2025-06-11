<?php

namespace TencentCloudSmsBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TencentCloudSmsBundle\Repository\AccountRepository;
use TencentCloudSmsBundle\Service\PhoneNumberInfoService;

#[AsCommand(
    name: 'tencent-cloud:sms:sync:phone-number-info',
    description: '同步手机号码信息',
)]
class SyncPhoneNumberInfoCommand extends Command
{
    public function __construct(
        private readonly PhoneNumberInfoService $phoneNumberInfoService,
        private readonly AccountRepository $accountRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $accounts = $this->accountRepository->findAll();

        foreach ($accounts as $account) {
            try {
                $this->phoneNumberInfoService->syncPhoneNumberInfo($account);
                $output->writeln(sprintf('账号 %s 手机号码信息同步完成', $account->getId()));
            } catch  (\Throwable $e) {
                $output->writeln(sprintf('账号 %s 手机号码信息同步失败: %s', $account->getId(), $e->getMessage()));
            }
        }

        return Command::SUCCESS;
    }
}
