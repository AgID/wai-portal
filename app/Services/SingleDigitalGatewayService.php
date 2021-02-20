<?php

namespace App\Services;

use App\Enums\Logs\EventType;
use App\Exceptions\SDGServiceException;
use Carbon\Carbon;
use GuzzleHttp\Client as APIClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;
use JsonSchema\Validator as JsonValidator;

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
        $this->storageDirectory = config('single-digital-gateway-service.storage_directory');
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
     * @param array the dataset
     *
     * @throws SDGServiceException if passed dataset is not a valid payload
     */
    public function sendStatisticsInformation($dataset): void
    {
        $uniqueId = $this->getUniqueID();
        $dataset->uniqueId = $uniqueId;

        $this->validatePayload($dataset);

        $requestDatetime = Carbon::now()->format('Y-m-d_H-i-s');
        Storage::disk($this->storageDisk)->put($this->storageDirectory . "/requests/req_{$requestDatetime}.json", json_encode($dataset, JSON_PRETTY_PRINT) . PHP_EOL);

        $response = $this->apiCall('/statistics/information-services', 'POST', [], (array) $dataset);

        $responseDatetime = Carbon::now()->format('Y-m-d_H-i-s');
        Storage::disk($this->storageDisk)->put($this->storageDirectory . "/responses/res_{$responseDatetime}.json", json_encode(json_decode($response), JSON_PRETTY_PRINT) . PHP_EOL);
    }

    /**
     * Check if data is a valid json against the information services payload schema.
     *
     * @param string the data to be validated
     *
     * @throws SDGServiceException if passed dataset is not a valid payload
     */
    public function validatePayload($dataset): void
    {
        $validator = new JsonValidator();
        $validator->validate($dataset, (object) ['$ref' => 'file://' . resource_path('data/sdg/schemas/informationServiceStats.json')]);

        if (!$validator->isValid()) {
            $validatorErrors = implode(array_map(function ($error) {
                return (empty($error['property']) ? '' : $error['property'] . ', ') . $error['message'] . "\n";
            }, $validator->getErrors()));

            throw new SDGServiceException("Invalid statistics data: \n" . $validatorErrors);
        }
    }

    /**
     * Make an API call to Single Digital Gateway Service.
     *
     * @param string $path the path
     * @param string $method the method
     * @param array $params the request parameter
     *
     * @throws SDGServiceException
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
                ]
            );

            $res = $client->request($method, $path, $options);
        } catch (GuzzleException $exception) {
            throw new SDGServiceException("Error during API call to SDG endpoint '{$path}': " . $exception->getMessage());
        }

        if (!isset($res) || is_null($res)) {
            throw new SDGServiceException("Error in the response during API call to SDG endpoint '{$path}'.");
        }

        if (200 != $res->getStatusCode()) {
            throw new SDGServiceException("Error response status from API call to SDG endpoint '{$path}: '" . $res->getStatusCode());
        }

        $response = json_decode($res->getBody(), true);

        logger()->notice(
            'Single Digital Gateway Service api call RESPONSE status code',
            [
                'event' => EventType::SINGLE_DIGITAL_GATEWAY_API_CALL_RESPONSE,
                'response' => $res->getStatusCode(),
            ]
        );

        return $response;
    }
}
