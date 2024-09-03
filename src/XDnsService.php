<?php

namespace XDns;

use RuntimeException;
use XDns\Model\Zone;
use XDns\Model\Record;
use XDns\Model\Provider;
use Symfony\Component\Yaml\Yaml;

class XDnsService
{
    private $zones = [];
    private $providers = [];

    // public function addZone(Zone $zone)
    // {
    //     $this->zones[$zone->getName()] = $zone;
    // }

    // public function getZone($name): Zone
    // {
    //     if (!isset($this->zones[$name])) {
    //         throw new RuntimeException("Zone not found: " . $name);
    //     }
    //     return $this->zones[$name];
    // }

    // public function getZones(): array
    // {
    //     return $this->zones;
    // }

    public function addProvider(Provider $provider)
    {
        $this->providers[$provider->getName()] = $provider;
    }

    public function getProvider($name): Provider
    {
        if (!isset($this->providers[$name])) {
            throw new RuntimeException("Unknown provider: " . $name);
        }
        return $this->providers[$name];
    }

    public function getProviders(): array
    {
        return $this->providers;
    }


    public static function fromConfigFilename(string $filename): self
    {
        $service = new self();
        if (!file_exists($filename)) {
            throw new RuntimeException('Config file not found: ' . $filename);
        }
        $yaml = file_get_contents($filename);
        $config = Yaml::parse($yaml);

        foreach ($config['providers'] as $providerName=>$providerConfig) {
            $provider = Provider::fromConfig($providerName, $providerConfig);
            $service->addProvider($provider);
        }
        return $service;
    }

    public function getZoneByFqzn(string $fqzn): Zone
    {
        $parts = explode('@', $fqzn);
        if (count($parts) != 2) {
            throw new RuntimeException('Invalid FQZN: ' . $fqzn);
        }
        $zoneName = $parts[0];
        $providerName = $parts[1];

        $provider = $this->getProvider($providerName);
        $adapter = $provider->getAdapter();
        $zone = $adapter->getZone($zoneName);
        $zone->setProvider($provider);
        return $zone;
    }
}
