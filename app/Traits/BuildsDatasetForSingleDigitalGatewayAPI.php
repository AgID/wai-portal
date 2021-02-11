<?php

namespace App\Traits;

use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\SDGServiceException;
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
    public function buildDatasetForSDG()
    {
        $analyticsService = app()->make('analytics-service');

        $days = config('single-digital-gateway-service.last_days');
        $columnSeparator = config('single-digital-gateway-service.url_column_separator_csv');
        $columnIndexUrl = config('single-digital-gateway-service.url_column_index_csv');
        $matomoRollupId = config('analytics-service.public_dashboard');
        $cronArchivingEnabled = config('analytics-service.cron_archiving_enabled');

        $arrayUrls = $this->getUrlsFromConfig($columnSeparator, $columnIndexUrl);

        date_default_timezone_set('UTC');
        $referencePeriod = new stdClass();
        $referencePeriod->startDate = config('analytics-service.start_date', date('Y-m-d\TH:i:s\Z', strtotime('-' . ($days) . ' days')));
        $referencePeriod->endDate = config('analytics-service.end_date', date('Y-m-d\TH:i:s\Z'));

        $data = new stdClass();
        $data->referencePeriod = $referencePeriod;
        $data->transferDate = date('Y-m-d\TH:i:s\Z');
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
                    $countryDays = $analyticsService->getCountriesInSegment($siteId, $days, $segmentDefinition);

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
     * @param string $separator the separator char for the CSV
     * @param string $index the index of the column containing the URL
     *
     * @throws SDGServiceException if the CSV is not valid or contains invalid values
     *
     * @return array the URLs to be used for the dataset build
     */
    protected function getUrlsFromConfig(string $separator, int $index): array
    {
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
