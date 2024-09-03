<?php

namespace XDns\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use XDns\Model\Record;
use RuntimeException;

class ZonePushCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('zone:push')
            ->setDescription('Push a zone to a provider B')
            ->addArgument(
                'fqzn',
                InputArgument::REQUIRED,
                'Zone'
            )
            ->addArgument(
                'providerName',
                InputArgument::OPTIONAL,
                'Provider name'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fqzn = $input->getArgument('fqzn');
        $providerName = $input->getArgument('providerName');

        $targets = [$providerName];
        $zone = $this->service->getZoneByFqzn($fqzn);
        if (!$providerName) {
            $targets = $zone->getTargets();
        }

        foreach ($targets as $providerName) {
            $output->writeLn('Pushing zone ' . $fqzn . ' to ' . $providerName);
            $provider = $this->service->getProvider($providerName);

            $adapter = $provider->getAdapter();
            $adapter->pushZone($zone);
            $output->writeLn('OK');
        }


        return 0;
    }
}
