<?php

namespace App\Adapter;

use Ehann\RedisRaw\PredisAdapter;
use Ehann\RedisRaw\RedisRawClientInterface;
use Illuminate\Support\Facades\Redis;

class RedisSentinelPredisAdapter extends PredisAdapter
{
    protected $index;

    public function __construct($index)
    {
        $this->index = $index;
    }

    public function connect($hostname = '127.0.0.1', $port = 6379, $db = 0, $password = null): RedisRawClientInterface
    {
        $this->redis = Redis::connection('database.redis.indexes.' . $this->index . '.sentinel')->client();

        return $this;
    }


}
