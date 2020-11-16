<?php

namespace App\Services;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use GuzzleHttp\Client as APIClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;

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
        $this->storageDisk = config('single-digital-gateway-service.storage_disk');
        $this->storageFolder = config('single-digital-gateway-service.storage_folder');
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
     * @return bool
     */
    public function sendStatisticsInformation($dataSet): bool
    {
        if ($this->payloadValidator($dataSet)) {
            $this->apiCall('/statistics/information-services', 'POST', [], (array) $dataSet);
        } else {
            logger()->critical(
                'Single Digital Gateway Service exception: sendStatisticsInformation',
                [
                    'event' => EventType::EXCEPTION,
                    'message' => 'The payload is not valid',
                ]
            );

            return false;
        }

        return true;
    }

    /**
     * Save the payload to the filesysem.
     *
     * @param object the dataset
     *
     * @return void
     */
    public function savePayloadToFilesystem($dataSet): void
    {
        Storage::disk($this->storageDisk)->put($this->storageFolder . '/payload.json', json_encode($dataSet));
    }

    /**
     * Get the payload from the filesysem.
     */
    public function getPayloadFromFilesystem()
    {
        return json_decode(Storage::disk($this->storageDisk)->get($this->storageFolder . '/payload.json'));
    }

    /**
     * Check if data is a valid json.
     *
     * @param string the data to be validated
     *
     * @return bool
     */
    public function payloadValidator($dataSetEncoded = null): bool
    {
        if (!$dataSetEncoded) {
            logger()->critical(
                'Single Digital Gateway Service payload validator',
                [
                    'event' => EventType::EXCEPTION,
                    'message' => 'The payload is empty',
                ]
            );

            return false;
        }

        $currenDirectory = dirname(__FILE__);
        $validator = new \JsonSchema\Validator();
        $validator->validate($dataSetEncoded, (object) ['$ref' => 'file://' . realpath($currenDirectory . '/schemas/informationServiceStats.json')]);

        if ($validator->isValid()) {
            return true;
        } else {
            foreach ($validator->getErrors() as $error) {
                logger()->critical(
                    'Single Digital Gateway Service Json schema validation error: ' . $error['message'],
                    [
                        'event' => EventType::EXCEPTION,
                        'exception_type' => ExceptionType::JSON_SCHEMA_VALIDATOR_ERROR,
                        'exception' => $error['message'],
                    ]
                );
            }

            return false;
        }
    }

    /**
     * Make an API call to Single Digital Gateway Service.
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
            logger()->notice(
                'Single Digital Gateway Service api call REQUEST',
                [
                    'event' => EventType::SINGLE_DIGITAL_GATEWAY_API_CALL_REQUEST,
                    'path' => $path,
                    'query' => json_encode($params),
                    'body' => json_encode($body),
                ]
            );
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

        logger()->notice(
            'Single Digital Gateway Service api call RESPONSE',
            [
                'event' => EventType::SINGLE_DIGITAL_GATEWAY_API_CALL_RESPONSE,
                'response' => json_encode($response),
            ]
        );

        return $response ?? $res->getStatusCode();
    }
}
