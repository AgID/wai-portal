<?php

namespace App\Adapter;

use Ehann\RedisRaw\PredisAdapter;
use Ehann\RedisRaw\RedisRawClientInterface;
use Illuminate\Support\Facades\Redis;

/**
 * Extended PredisAdapter with Redis Sentinel support.
 *
 * @see \Ehann\RedisRaw\PredisAdapter
 */
class RedisSentinelPredisAdapter extends PredisAdapter
{
    /**
     * The RediSearch index name.
     *
     * @var string the index name
     */
    protected $index;

    /**
     * Default constructor.
     *
     * @param string $index the index name
     */
    public function __construct($index)
    {
        $this->index = $index;
    }

    /**
     * Open connection to RediSearch server using Redis Sentinels.
     * NOTE: all method input parameters are ignored; the connection configuration
     *       is pulled directly from the databases config file.
     *
     * @param string $hostname ignored
     * @param int $port ignored
     * @param int $db ignored
     * @param null $password ignored
     *
     * @return RedisRawClientInterface the client with an open connection
     */
    public function connect($hostname = '127.0.0.1', $port = 6379, $db = 0, $password = null): RedisRawClientInterface
    {
        $this->redis = Redis::connection('database.redis.indexes.' . $this->index . '.sentinel')->client();

        return $this;
    }
}
