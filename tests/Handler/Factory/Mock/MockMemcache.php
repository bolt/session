<?php

namespace Bolt\Session\Tests\Handler\Factory\Mock;

class MockMemcache extends \Memcache
{
    private $servers = [];

    public function addServer(
        $host,
        $port = 11211,
        $persistent = true,
        $weight = null,
        $timeout = 1,
        $retryInterval = 15,
        $status = true,
        callable $failureCallback = null
    ) {
        $this->servers[] = [
            'host'           => $host,
            'port'           => $port,
            'persistent'     => $persistent,
            'weight'         => $weight,
            'timeout'        => $timeout,
            'retry_interval' => $retryInterval,
        ];
    }

    public function getServers()
    {
        return $this->servers;
    }
}
