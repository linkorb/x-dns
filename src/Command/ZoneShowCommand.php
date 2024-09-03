<?php

namespace XDns\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use RuntimeException;
use Symfony\Component\Console\Output\Output;

class ZoneShowCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('zone:show')
            ->setDescription('Show zone details from the selected provider')
            ->addArgument(
                'fqzn',
                InputArgument::REQUIRED,
                'The fully qualified zone name (with @provider postfix)'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fqzn = $input->getArgument('fqzn');
        $zone = $this->service->getZoneByFqzn($fqzn);

        $output->writeLn("Zone: " . $zone->getFqzn());
        $output->writeLn("Records: " . count($zone->getRecords()));
        $output->writeLn("Targets: " . implode(", ", $zone->getTargets()));

        foreach ($zone->getRecords() as $record) {
            $output->writeLn('    <comment>' . $record->getName() . "</comment> " . $record->getType() . " <info>" . $record->getValue() . "</info> (ttl " . $record->getTtl() . ')');
        }
        return Command::SUCCESS;
    }
}
