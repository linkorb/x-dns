<?php

namespace XDns\Adapter;

use XDns\Model\Zone;
use XDns\Model\Record;
use Transip\Api\Library\TransipAPI;
use Symfony\Component\Yaml\Yaml;

class FileAdapter implements XDnsAdapterInterface
{
    protected $path;

    protected $zones = [];

    public static function fromConfig(array $config): self
    {
        $adapter = new self($config['path']);
        return $adapter;
    }

    public function __construct(string $path)
    {
        $this->path = $path;
        if (!file_exists($path)) {
            throw new \RuntimeException('File not found: ' . $path);
        }
        // find all yaml and yml files in the path
        $files = glob($path . '/*.yml');
        $files = array_merge($files, glob($path . '/*.yaml'));
        foreach ($files as $file) {
            $yaml = file_get_contents($file);
            $config = Yaml::parse($yaml);
            $zone = new Zone();
            $zone->setName($config['name']);
            foreach ($config['targets'] ?? [] as $targetName) {
                $zone->addTarget($targetName);
            }

            foreach ($config['records'] as $recordName => $recordConfig) {
                $record = new Record();
                $record->setName(trim($recordConfig['name']));
                $record->setType(trim(strtoupper($recordConfig['type'])));
                $record->setValue(trim($recordConfig['value']));
                $record->setTtl(trim($recordConfig['ttl']));
                $zone->setRecord($record);
            }
            $this->zones[$zone->getName()] = $zone;
        }
    }

    public function getZoneNames(): array
    {
        $zoneNames = [];
        foreach ($this->zones as $zone) {
            $zoneNames[] = $zone->getName();
        }
        return $zoneNames;
    }

    public function hasZone(string $zoneName): bool
    {
        return isset($this->zones[$zoneName]);
    }

    public function getZone(string $zoneName): Zone
    {
        if (!$this->hasZone($zoneName)) {
            throw new \RuntimeException('No such zone at provider: ' . $zoneName);
        }
        return $this->zones[$zoneName];
    }

}
