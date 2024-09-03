<?php

namespace XDns\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use RuntimeException;
use Symfony\Component\Console\Output\Output;

class ProviderListCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('provider:list')
            ->setDescription('List providers')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $providers = $this->service->getProviders();
        $output->writeLn("Providers: " . count($providers));
        foreach ($providers as $provider) {
            $output->writeLn('  - ' .$provider->getName());
        }
        return Command::SUCCESS;
    }
}
