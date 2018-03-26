<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ehann\RediSearch\Index;
use Ehann\RediSearch\Redis\RedisClient;
use Exception;

class SearchIPAListController extends Controller
{
    /**
     * Return elements found in IPA list
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Ehann\RediSearch\Exceptions\InvalidRedisClientClassException
     */
    public function search(Request $request)
    {
        $result = [];
        if (isset($request->q)) {
            // Remove negation from query which can be slow and cause high CPU consumption
            // See: http://redisearch.io/Query_Syntax/#pure"_"negative"_"queries
            $query = str_replace('-', '', $request->q).'*';

            $clientClassName = config('database.redis.client') == 'phpredis' ? 'Redis' : 'Predis\Client';
            $IPAIndex = new Index(new RedisClient($clientClassName, config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');
            try {
                $result = $IPAIndex->limit(0, 100)
                    ->sortBy('name')
                    ->inFields(3, ['ipa_code', 'name', 'city'])
                    ->search($query)
                    ->getDocuments();
            } catch (Exception $e) {
                // RediSearch returned an error, probably malformed query or index not found.
                // Please notify me!
                if (!app()->environment('testing')) {
                    logger()->error($e);
                }
            }
        }
        return response()->json($result);
    }
}
