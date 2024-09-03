<?php

namespace XDns\Adapter;

use XDns\Model\Zone;
use XDns\Model\Provider;

use RuntimeException;

interface XDnsAdapterInterface
{
    // public function addZone(Zone $zone);
    public function hasZone(string $name): bool;
    public function getZone(string $name): Zone;
    public function getZoneNames(): array;
    public static function fromConfig(array $config): self;
}
