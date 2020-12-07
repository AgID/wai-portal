<?php

namespace App\Services;

use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use GuzzleHttp\Client as APIClient;
use GuzzleHttp\Exception\GuzzleException;

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

    public function __construct()
    {
        $this->serviceBaseUri = config('kong-service.endpoint_url');
    }

    public function getConsumer(string $idConsumer): ?array
    {
        $data = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        return $this->apiCall($data, 'GET', '/consumers/' . $idConsumer);
    }

    public function getClient(string $idConsumer): ?array
    {
        $data = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        return $this->apiCall($data, 'GET', '/consumers/' . $idConsumer . '/oauth2');
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

        return $this->apiCall($data, 'PUT', '/consumers/' . $idConsumer);
    }

    protected function apiCall(array $data, string $method = 'GET', string $path = '/'): array
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
