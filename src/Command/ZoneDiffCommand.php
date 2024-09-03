<?php

namespace XDns\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use XDns\Model\Record;
use RuntimeException;

class ZoneDiffCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('zone:diff')
            ->setDescription('Diff two zones')
            ->addArgument(
                'fqznA',
                InputArgument::REQUIRED,
                'zone A'
            )
            ->addArgument(
                'target',
                InputArgument::OPTIONAL,
                'fqzn of zone B or provider name'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $fqznA = $input->getArgument('fqznA');
        $zoneA = $this->service->getZoneByFqzn($fqznA);

        $target = $input->getArgument('target');


        $targets = [$target];

        if (!$target) {
            $targets = $zoneA->getTargets();
        }

        foreach ($targets as $target) {

            $fqznB = $target;
            if (strpos($target, '@') === false) {
                // if target is just a provider name, prepend the zone name
                $fqznB = $zoneA->getName() . '@' . $target;
            }

            $zoneB = $this->service->getZoneByFqzn($fqznB);


            $output->writeLn('Diff between ' . $zoneA->getFqzn() . ' and ' . $zoneB->getFqzn());

            foreach ($zoneA->getRecords() as $record) {
                $key = $record->getKey();
                if ($zoneB->hasKey($key)) {
                    $recordB = $zoneB->getRecord($key);
                    if ($record->getString() == $recordB->getString()) {
                        $output->writeLn('  ' . $record->getString());
                    } else {
                        $output->writeLn('> ' . $record->getString());
                        $output->writeLn('< ' . $recordB->getString());
                    }
                } else {
                    $output->writeLn('> ' . $record->getString());
                }
            }
            foreach ($zoneB->getRecords() as $record) {
                $key = $record->getKey();
                if (!$zoneA->hasKey($key)) {
                    $output->writeLn('< ' . $record->getString());
                }
            }
        }

        return 0;
    }
}
