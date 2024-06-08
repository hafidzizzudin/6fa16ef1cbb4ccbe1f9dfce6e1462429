<?php

namespace Src\System;

use Enqueue\Redis\PRedis;
use Enqueue\Redis\RedisConnectionFactory;
use Interop\Queue\Context;

class RedisQueue
{
    private Context $context;

    public function __construct()
    {
        $connectionFactory = new RedisConnectionFactory([
            'host' => $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
            'password' => $_ENV['REDIS_PASSWORD'],
            'scheme_extensions' => ['predis'],
        ]);

        $this->context = $connectionFactory->createContext();
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}
