<?php

namespace App\Services;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Exceptions\CommandErrorException;
use GuzzleHttp\Client as APIClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Matomo implementation of Analytics Service.
 */
class SingleDigitalGatewayService
{
    /**
     * Local service URL.
     *
     * @var string the local URL
     */
    protected $serviceBaseUri;

    /**
     * Public service URL.
     *
     * @var string the public URL
     */
    protected $servicePublicUrl;

    /**
     * SSL verification flag.
     *
     * @var bool true to check SSL certificates, false to skip
     */
    protected $SSLVerify;

    /**
     * Authentication api key for API calls.
     *
     * @var string the api key provided to the member states by the SDG management
     */
    protected $apiKey;

    /**
     * Create a new Matomo Service instance.
     */
    public function __construct()
    {
        $this->serviceBaseUri = config('sdg-service.api_base_uri');
        // $this->servicePublicUrl = config('sdg-service.public_url');
        $this->SSLVerify = config('sdg-service.ssl_verify');
        $this->apiKey = config('sdg-service.api_key');
    }

    /**
     * Get the Unique ID.
     *
     * @return array The Unique ID for feedback submission for a specific reference period collected
     */
    public function getUniqueID(): array
    {
        return $this->apiCall('/unique-id');
    }

    /**
     * Make an API call to Analytics Service.
     *
     * @param string $path the path
     * @param array $params the request parameter
     *
     * @throws CommandErrorException if command finishes with error status
     *
     * @return array the JSON response
     */
    protected function apiCall(string $path, array $params = []): array
    {
        try {
            $client = new APIClient(['base_uri' => $this->serviceBaseUri]);
            $res = $client->request('GET', $path, [
                'query' => array_merge($params, [
                    'module' => 'API',
                    'format' => 'JSON',
                    'headers' => [
                        'x-api-key' => $this->apiKey,
                    ],
                ]),
                'verify' => $this->SSLVerify,
            ]);
        } catch (GuzzleException $exception) {
            logger()->critical(
                'Single Digital Gateway Service exception: ' . $exception->getMessage(),
                [
                    'event' => EventType::EXCEPTION,
                    'exception_type' => ExceptionType::SDG_GENERIC_ERROR,
                    'exception' => $exception,
                ]
            );
        }

        $response = json_decode($res->getBody(), true);

        if (!empty($response['result']) && 'error' === $response['result']) {
            throw new CommandErrorException($response['message']);
        }

        return $response;
    }
}
