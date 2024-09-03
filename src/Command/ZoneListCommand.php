<?php

namespace XDns\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use RuntimeException;
use Symfony\Component\Console\Output\Output;

class ZoneListCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('zone:list')
            ->setDescription('List zones at selected provider')
            ->addArgument(
                'providerName',
                InputArgument::REQUIRED,
                'The provider name'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $providerName = $input->getArgument('providerName');
        $provider = $this->service->getProvider($providerName);
        $adapter = $provider->getAdapter();
        $zoneNames = $adapter->getZoneNames();
        foreach ($zoneNames as $zoneName) {
            $output->writeLn($zoneName);
        }
        return Command::SUCCESS;
    }
}
