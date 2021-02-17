<?php

namespace App\Traits;

use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\SDGServiceException;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Storage;
use stdClass;

/**
 * Get the dataset to Single Digital Gateway API for the Statistics on Information Services.
 */
trait BuildsDatasetForSingleDigitalGatewayAPI
{
    use ParseUrls;

    /**
     * Build the dataset to Single Digital Gateway API for the Statistics on Information Services.
     *
     * @return object the dataset
     */
    public function buildDatasetForSDG(?string $rangePeriod = null)
    {
        $analyticsService = app()->make('analytics-service');

        $matomoRollupId = config('analytics-service.public_dashboard');
        $cronArchivingEnabled = config('analytics-service.cron_archiving_enabled');

        if (is_string($rangePeriod)) {
            if (false === strpos($rangePeriod, ',')) {
                throw new SDGServiceException('Invalid period parameter');
            }
            list($startDateString, $endDateString) = explode(',', $rangePeriod);

            $startDate = Carbon::createFromFormat('Y-m-d', $startDateString, 'UTC')->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $endDateString, 'UTC')->endOfDay();

            if ($startDate->toDateString() !== $startDateString || $endDate->toDateString() !== $endDateString) {
                throw new SDGServiceException('Invalid date in period parameter');
            }
        } else {
            $startDate = Carbon::now('UTC')->startOfMonth()->subMonth(); // firstDayofPreviousMonth
            $endDate = Carbon::now('UTC')->startofMonth()->subMonth()->endOfMonth(); // lastDayofPreviousMonth

            $rangePeriod = implode(',', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);
        }

        $arrayUrls = $this->getUrls();

        $referencePeriod = new stdClass();
        $referencePeriod->startDate = $startDate->toIso8601ZuluString();
        $referencePeriod->endDate = $endDate->toIso8601ZuluString();

        $data = new stdClass();
        $data->referencePeriod = $referencePeriod;
        $data->transferDate = Carbon::now('UTC')->toIso8601ZuluString();
        $data->transferType = 'API';
        $data->nbEntries = 0;
        $data->sources = [];

        try {
            $allSegments = $analyticsService->getAllSegments();
            $allSegmentsNames = array_column($allSegments, 'idsegment');
            $sdgDeviceTypes = [
                'PC',
                'Smartphone',
                'Tablet',
                'Others',
            ];

            foreach ($arrayUrls as $url) {
                if (!is_string($url)) {
                    continue;
                }

                $source = new stdClass();
                $source->sourceUrl = trim($url);

                if ((0 !== strpos($source->sourceUrl, 'http')) || !filter_var($source->sourceUrl, FILTER_VALIDATE_URL)) {
                    report(new SDGServiceException("Error during dataset build: '{$source->sourceUrl}' is not a valid url."));

                    continue;
                }

                $siteId = $analyticsService->getSitesIdFromUrl($this->getFqdnFromUrl($source->sourceUrl));

                if (!empty($siteId)) {
                    $siteId = $siteId[0]['idsite'];
                } else {
                    report(new SDGServiceException("Error during dataset build: site id not found for '{$source->sourceUrl}' url."));

                    continue;
                }

                $newSegmentsAdded = false;

                foreach ($sdgDeviceTypes as $sdgDeviceType) {
                    $segmentName = $source->sourceUrl . '___' . $sdgDeviceType;
                    $segmentDefinition = 'pageUrl==' . urlencode($source->sourceUrl) . ';' . static::getDeviceTypeSegmentParameter($sdgDeviceType);
                    $segmentExists = in_array($segmentName, $allSegmentsNames);

                    if (!$segmentExists) {
                        $analyticsService->addSegment($siteId, $segmentDefinition, $segmentName);
                        $newSegmentsAdded = true;
                    }
                }

                if ($newSegmentsAdded && $cronArchivingEnabled) {
                    report(new SDGServiceException("Cron archiving is enabled and new segments has been added: reports for '{$source->sourceUrl}' will be available after the next archiving job."));

                    continue;
                }

                $source->statistics = [];
                $sourceStatistics = [];

                foreach ($sdgDeviceTypes as $sdgDeviceType) {
                    $segmentDefinition = 'pageUrl==' . urlencode($source->sourceUrl) . ';' . static::getDeviceTypeSegmentParameter($sdgDeviceType);
                    $countryDays = $analyticsService->getCountriesInSegment($siteId, $rangePeriod, $segmentDefinition);

                    foreach ($countryDays as $countryDay) {
                        foreach ($countryDay as $country) {
                            $deviceCountry = $sdgDeviceType . '_' . $country['code'];
                            $sourceStatistics[$deviceCountry]['nbVisits'] += $country['nb_visits'];
                            $sourceStatistics[$deviceCountry]['originatingCountry'] = $country['code'];
                            $sourceStatistics[$deviceCountry]['deviceType'] = $sdgDeviceType;
                        }
                    }

                    $source->statistics += array_values($sourceStatistics);
                }

                array_push($data->sources, $source);
            }

            $data->nbEntries = count($data->sources);
        } catch (BindingResolutionException $exception) {
            throw new SDGServiceException('Unable to bind to the Analytics Service: ' . $exception->getMessage());
        } catch (AnalyticsServiceException $exception) {
            throw new SDGServiceException('Unable to contact to the Analytics Service: ' . $exception->getMessage());
        }

        return $data;
    }

    /**
     * Return an array of URLs to be used for the dataset build.
     *
     * @return array the URLs to be used for the dataset build
     */
    protected function getUrls(): array
    {
        if ('csv' === strtolower(config('single-digital-gateway-service.urls_file_format', 'json'))) {
            return $this->getUrlsFromCsv();
        } else {
            return $this->getUrlsFromJson();
        }
    }

    /**
     * Return an array of URLs to be used for the dataset build.
     *
     * @throws SDGServiceException if the CSV is not valid or contains invalid values
     *
     * @return array the URLs to be used for the dataset build
     */
    protected function getUrlsFromCsv(): array
    {
        $separator = config('single-digital-gateway-service.url_column_separator_csv');
        $index = config('single-digital-gateway-service.url_column_index_csv');

        try {
            $csvContents = file(Storage::disk('persistent')->path('sdg/urls.csv'));
        } catch (Exception $e) {
            throw new SDGServiceException("Error reading the CSV file populated with SDG URLs.\n" . $e->getMessage());
        }

        $urls = array_map(function ($row) use ($separator, $index) {
            $rowArray = str_getcsv($row, $separator);

            if (!isset($rowArray[$index])) {
                report(new SDGServiceException('Error during dataset build: CSV file not well formed.'));

                return;
            }

            return $rowArray[$index];
        }, $csvContents);

        return $urls;
    }

    /**
     * Return an array of URLs to be used for the dataset build.
     *
     * @throws SDGServiceException if the JSON is not valid or contains invalid values
     *
     * @return array the URLs to be used for the dataset build
     */
    protected function getUrlsFromJson(): array
    {
        $urlsArrayPath = config('single-digital-gateway-service.url_array_path_json');
        $urlsKey = config('single-digital-gateway-service.url_key_json');

        try {
            $jsonContents = Storage::disk('persistent')->get('sdg/urls.json');
            $urlsArray = json_decode($jsonContents, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new SDGServiceException("Error reading the JSON file populated with SDG URLs.\n" . $e->getMessage());
        }

        $urlsArrayPathSegments = array_filter(explode('.', $urlsArrayPath));

        foreach ($urlsArrayPathSegments as $urlsArrayPathSegment) {
            if (!array_key_exists($urlsArrayPathSegment, $urlsArray)) {
                throw new SDGServiceException('Error during dataset build: JSON file not well formed.');
            }

            if (!is_array($urlsArray[$urlsArrayPathSegment])) {
                throw new SDGServiceException('Error during dataset build: JSON file not well formed.');
            }

            $urlsArray = $urlsArray[$urlsArrayPathSegment];
        }

        $urls = array_filter(collect($urlsArray)->pluck($urlsKey)->all());

        return $urls;
    }

    /**
     * Return segment parameter for a specific SDG device type.
     *
     * @param string $type the SDG device type
     *
     * @return array the segment parameter for the provided device type
     */
    private static function getDeviceTypeSegmentParameter(string $type): string
    {
        switch (strtolower($type)) {
            case 'pc':
                return 'deviceType==desktop';
            case 'smartphone':
                return 'deviceType==smartphone';
            case 'tablet':
                return 'deviceType==tablet';
            case 'others':
                return 'deviceType!=desktop;deviceType!=smartphone;deviceType!=tablet';
        }
    }

    /**
     * Return SDG country codes from ISO-3166 to SDG standard.
     * N.B. Greece is a documented exception required by the EC.
     *
     * @param string $code the country code
     *
     * @return string the SDG country code
     */
    private static function getSdgCountryCode(string $code): string
    {
        if ('gr' === strtolower($code)) {
            return 'EL';
        }

        return strtoupper($code);
    }
}
