<?php

namespace XDns\Command;

use Symfony\Component\Console\Command\Command;
use XDns\Adapter\TransIPAdapter;
use XDns\XDnsService;
use RuntimeException;

abstract class BaseCommand extends Command
{
    protected $service;

    public function __construct(XDnsService $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    // protected function getAdapter()
    // {
    //     $username = getenv('TRANSIP_USERNAME');
    //     $key = getenv('TRANSIP_KEY');
    //     if (!$username || !$key) {
    //         throw new RuntimeException("Missing environment variables to instantiate adapter");
    //     }
    //     $adapter = new TransIPAdapter($username, $key);
    //     return $adapter;
    // }
}
