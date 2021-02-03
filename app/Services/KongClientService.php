<?php

namespace App\Services;

use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use App\Exceptions\InvalidKeyException;
use GuzzleHttp\Client as APIClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Redis;

/**
 * Matomo implementation of Analytics Service.
 */
class KongClientService
{
    /**
     * Local service URL.
     *
     * @var string the local URL
     */
    protected $serviceBaseUri;

    protected $redisCache;

    public function __construct()
    {
        $this->serviceBaseUri = config('kong-service.admin_api_url');
        $this->redisCache = Redis::connection(env('CACHE_CONNECTION'))->client();
    }

    public function getConsumer(string $idConsumer): ?array
    {
        $data = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        $consumer = $this->redisCache->get('kong:consumer:' . $idConsumer);

        if (null !== $consumer) {
            $consumer = json_decode($consumer);
            $consumer = (array) $consumer;

            return $consumer;
        }

        $consumer = $this->apiCall($data, 'GET', '/consumers/' . $idConsumer);

        $this->redisCache->set('kong:consumer:' . $idConsumer, json_encode($consumer));

        return $consumer;
    }

    public function getClient(string $idConsumer): ?array
    {
        $data = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        $client = $this->redisCache->get('kong:client:' . $idConsumer);

        if (null !== $client) {
            $client = json_decode($client);
            if (null === $client && !is_array($client)) {
                throw new InvalidKeyException('client[0] not found');
            }
            $client = (array) $client[0];

            return $client;
        }

        $client = $this->apiCall($data, 'GET', '/consumers/' . $idConsumer . '/oauth2');
        $client = $client['data'];

        if (null === $client && !is_array($client)) {
            throw new InvalidKeyException('client[0] not found');
        }

        $this->redisCache->set('kong:client:' . $idConsumer, json_encode($client));

        return $client[0];
    }

    public function makeConsumer(string $username, string $customID): array
    {
        $data = [
            'json' => [
                'username' => $username,
                'custom_id' => $customID,
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];
        $response = $this->apiCall($data, 'POST', '/consumers');

        return $this->makeClient($response['username']);
    }

    public function makeClient(string $name): array
    {
        $body = [
            'form_params' => [
                'name' => $name . '-oauth2',
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];

        return $this->apiCall($body, 'POST', '/consumers/' . $name . '/oauth2');
    }

    public function updateClient(string $idConsumer, array $newData): array
    {
        $data = [
            'json' => $newData,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        $consumer = $this->apiCall($data, 'PUT', '/consumers/' . $idConsumer);
        $this->redisCache->set('kong:consumer:' . $idConsumer, json_encode($consumer));

        return $consumer;
    }

    public function deleteConsumer(string $idConsumer): bool
    {
        $data = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        $consumer = $this->apiCall($data, 'DELETE', '/consumers/' . $idConsumer);

        return true;
    }

    protected function apiCall(array $data, string $method = 'GET', string $path = '/'): ?array
    {
        try {
            $client = new APIClient();
            $res = $client->request($method, $this->serviceBaseUri . $path, $data);
        } catch (GuzzleException $exception) {
            throw new AnalyticsServiceException($exception->getMessage());
        }

        $response = json_decode((string) $res->getBody(), true);

        if (!empty($response['result']) && 'error' === $response['result']) {
            throw new CommandErrorException($response['message']);
        }

        return $response;
    }
}
