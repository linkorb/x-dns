<?php

namespace XDns\Adapter;

use XDns\Model\Zone;
use XDns\Model\Record;
use Transip\Api\Library\TransipAPI;

class TransIPAdapter implements XDnsAdapterInterface
{
    protected $client;

    public static function fromConfig(array $config): self
    {
        $client = new TransipAPI($config['username'], $config['key']);
        $adapter = new self($client);
        return $adapter;
    }

    public function __construct(TransipAPI $client)
    {
        $this->client = $client;
    }

    public function getZoneNames(): array
    {
        $zoneNames = [];
        $domains = $this->client->domains()->getAll();
        foreach ($domains as $domain) {
            $zoneNames[] = $domain->getName();
        }
        return $zoneNames;
    }

    public function hasZone(string $zoneName): bool
    {
        return false;
    }

    public function getZone($zoneName): Zone
    {
        $includes = ['nameservers', 'contacts'];
        $domain = $this->client->domains()->getByName($zoneName, $includes);

        $domainDns = $this->client->domainDns()->getByDomainName($zoneName);

        // print_r($domain);
        // print_r($domainDns);
        // exit(PHP_EOL);

        $zone = new Zone();
        $zone->setName($zoneName);

        $dnsRecords = [];
        foreach ($domainDns as $entry) {
            $record = new Record();

            $record->setName($entry->getName());
            $record->setTtl($entry->getExpire());
            $record->setType($entry->getType());
            $record->setValue($entry->getContent());
            $key = $record->getKey();
            // $records[$key] = $record;
            $zone->setRecord($record);
        }
        return $zone;
    }

    public function pushZone(Zone $zone)
    {

        foreach ($zone->getRecords() as $record) {
            $entry = new \Transip\Api\Library\Entity\Domain\DnsEntry();
            $entry->setName($record->getName());
            $entry->setExpire($record->getTtl());
            $entry->setType($record->getType());
            $entry->setContent($record->getValue());
            $entries[] = $entry;
        }
        // print_r($entries);exit();
        $update = $this->client->domainDns()->update($zone->getName(), $entries);


    }

}
