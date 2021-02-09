<?php

namespace App\Services;

use App\Exceptions\ApiGatewayServiceException;
use App\Exceptions\CommandErrorException;
use App\Exceptions\InvalidCredentialException;
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
                throw new InvalidCredentialException('OAuth2 credentials not found');
            }

            return (array) $client;
        }

        $clients = $this->apiCall($data, 'GET', '/consumers/' . $idConsumer . '/oauth2');
        $clients = $clients['data'];

        if (null === $clients && !is_array($clients)) {
            throw new InvalidCredentialException('OAuth2 credentials not found');
        }

        if(count($clients) > 1){
            /* Se sono state create più credenziali oauth recupero le più recenti */
            usort($clients, function ($a, $b) {
                return $b['created_at'] <=> $a['created_at'];
            });
            $client = $clients[0];
        }
        else{
            $client = $clients[0];
        }

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

        $response = $this->apiCall($data, 'POST', '/consumers');

        $this->redisCache->set('kong:consumer:' . $response["id"], json_encode($response));

        return $this->makeClient($response['username'], $response["id"]);
    }

    public function makeClient(string $name, string $idConsumer): array
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

        $response = $this->apiCall($body, 'POST', '/consumers/' . $name . '/oauth2');

        $this->redisCache->set('kong:client:' . $idConsumer, json_encode($response));
        
        return $response;
    }

    public function regenerateSecret(string $name, string $idConsumer, string $idClient)
    {
        $body = [
            'form_params' => [
                'name' => $name . '-oauth2',
                'client_id' => $idClient
            ],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];

        $response = $this->apiCall($body, 'PUT', '/consumers/' . $name . '/oauth2/'. $idClient);

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
            throw new ApiGatewayServiceException($exception->getMessage());
        }

        $response = json_decode((string) $res->getBody(), true);

        if (!empty($response['result']) && 'error' === $response['result']) {
            throw new CommandErrorException($response['message']);
        }

        return $response;
    }
}
