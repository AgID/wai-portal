<?php

namespace App\Services;

use App\Exceptions\ApiGatewayServiceException;
use App\Exceptions\InvalidCredentialException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

/**
 * Kong Services Api.
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
        $this->redisCache = Redis::connection(config('redis-connections.cache_connection'))->client();
    }

    public function getConsumer(string $idConsumer): ?array
    {
        $consumer = $this->redisCache->get('kong:consumer:' . $idConsumer);

        if (null !== $consumer) {
            $consumer = json_decode($consumer);
            $consumer = (array) $consumer;

            return $consumer;
        }

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $consumer = $this->apiCall($headers, [], 'GET', '/consumers/' . $idConsumer);

        $this->redisCache->set('kong:consumer:' . $idConsumer, json_encode($consumer));

        return $consumer;
    }

    public function getClient(string $idConsumer): ?array
    {
        $client = $this->redisCache->get('kong:client:' . $idConsumer);

        if (null !== $client) {
            $client = json_decode($client);

            if (null === $client && !is_array($client)) {
                throw new InvalidCredentialException('OAuth2 credentials not found');
            }

            return (array) $client;
        }

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $clients = $this->apiCall($headers, [], 'GET', '/consumers/' . $idConsumer . '/oauth2');
        $clients = $clients['data'];

        if (null === $clients && !is_array($clients)) {
            throw new InvalidCredentialException('OAuth2 credentials not found');
        }

        $client = $clients[0];

        $this->redisCache->set('kong:client:' . $idConsumer, json_encode($client));

        return $client;
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

        $response = $this->apiCall($data['headers'], $data['json'], 'POST', '/consumers');

        $this->redisCache->set('kong:consumer:' . $response['id'], json_encode($response));

        return $this->makeClient($response['username'], $response['id']);
    }

    public function makeClient(string $name, string $idConsumer): array
    {
        $data = [
            'form_params' => [
                'name' => $name . '-oauth2',
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $response = $this->apiCall($data['headers'], $data['form_params'], 'POST', '/consumers/' . $name . '/oauth2', true);

        $this->redisCache->set('kong:client:' . $idConsumer, json_encode($response));

        return $response;
    }

    public function regenerateSecret(string $name, string $idConsumer, string $idClient)
    {
        $data = [
            'form_params' => [
                'name' => $name . '-oauth2',
                'client_id' => $idClient,
            ],
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];

        $response = $this->apiCall($data['headers'], $data['form_params'], 'PUT', '/consumers/' . $name . '/oauth2/' . $idClient, true);

        $this->redisCache->set('kong:client:' . $idConsumer, json_encode($response));

        return $response;
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

        $consumer = $this->apiCall($data['headers'], $data['json'], 'PUT', '/consumers/' . $idConsumer);
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

        $consumer = $this->apiCall($data['headers'], [], 'DELETE', '/consumers/' . $idConsumer);

        return true;
    }

    protected function apiCall(array $headers = [], array $body = [], string $method = 'GET', string $path = '/', bool $isForm = false): ?array
    {
        $method = strtolower($method);

        try {
            if ($isForm) {
                $res = Http::withHeaders($headers)
                    ->asForm()
                    ->{$method}(
                        $this->serviceBaseUri . $path,
                        $body
                    );
            } else {
                $res = Http::withHeaders($headers)
                    ->{$method}(
                        $this->serviceBaseUri . $path,
                        $body
                    );
            }
        } catch (RequestException $exception) {
            throw new ApiGatewayServiceException($exception->getMessage());
        }

        return $res->json();
    }
}
