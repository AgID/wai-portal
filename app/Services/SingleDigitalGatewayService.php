<?php

namespace App\Services;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Exceptions\AnalyticsServiceException;
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
        $this->serviceBaseUri = config('single-digital-gateway-service.api_public_url');
        $this->SSLVerify = config('single-digital-gateway-service.ssl_verify');
        $this->apiKey = config('single-digital-gateway-service.api_key');
    }

    /**
     * Get the Unique ID.
     *
     * @return string The Unique ID for feedback submission for a specific reference period collected
     */
    public function getUniqueID(): string
    {
        return $this->apiCall('/unique-id');
    }

    /**
     * Send a dataset to Single Digital Gateway API for the Statistics on Information Services.
     *
     * @param arry the dataset
     *
     * @return string The status response
     */
    public function sendStatisticsInformation($dataSet): void
    {
        $this->apiCall('/statistics/information-services', 'POST', [], (array) $dataSet);
    }

    /**
     * Make an API call to Analytics Service.
     *
     * @param string $path the path
     * @param string $method the method
     * @param array $params the request parameter
     *
     * @throws CommandErrorException if command finishes with error status
     */
    protected function apiCall(string $path, string $method = 'GET', array $params = [], array $body = null)
    {
        try {
            $client = new APIClient(['base_uri' => $this->serviceBaseUri]);
            $options = [
                'query' => $params,
                'headers' => [
                    'X-API-Key' => $this->apiKey,
                ],
                'verify' => $this->SSLVerify,
                'json' => $body,
            ];
            $res = $client->request($method, $path, $options);
        } catch (GuzzleException $exception) {
            logger()->critical(
                'Single Digital Gateway Service exception: ' . $exception->getMessage(),
                [
                    'event' => EventType::EXCEPTION,
                    'exception_type' => ExceptionType::SINGLE_DIGITAL_GATEWAY_GENERIC_ERROR,
                    'exception' => $exception,
                ]
            );
            throw new AnalyticsServiceException('Si è verificato un errore: ' . $exception->getMessage());
        }

        if (!isset($res) || is_null($res)) {
            throw new AnalyticsServiceException('Si è verificato un errore');
        }

        if (200 != $res->getStatusCode()) {
            throw new AnalyticsServiceException('Si è verificato un errore: ' . $res->getStatusCode());
        }

        $response = json_decode($res->getBody(), true);

        return $response ?? $res->getStatusCode();
    }
}
