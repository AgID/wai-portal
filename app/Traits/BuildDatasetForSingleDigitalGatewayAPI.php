<?php

namespace App\Traits;

use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Storage;
use stdClass;

/**
 * Get the dataset to Single Digital Gateway API for the Statistics on Information Services.
 */
trait BuildDatasetForSingleDigitalGatewayAPI
{
    /**
     * Build the dataset to Single Digital Gateway API for the Statistics on Information Services.
     *
     * @return object the dataset
     */
    public function buildDatasetForSDG()
    {
        $arrayUrls = $this->getUrlsFromConfig('urls');

        $analyticsService = app()->make('analytics-service');

        $days = config('single-digital-gateway-service.last_days');
        $columnIndexUrl = config('single-digital-gateway-service.url_column_index_csv');
        $matomoRollupId = config('analytics-service.public_dashboard');

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
            if ($matomoRollupId) {
                $definedSegments = $analyticsService->getAllSegments();

                foreach ($arrayUrls as $urlRow) {
                    $source = new stdClass();
                    $source->sourceUrl = $urlRow[$columnIndexUrl];

                    if (!filter_var($source->sourceUrl, FILTER_VALIDATE_URL)) {
                        continue;
                    }
                    $source->statistics = [];

                    $segmentExists = array_search($source->sourceUrl, array_column($definedSegments, 'name'));

                    $segment = urlencode('pageUrl==' . $source->sourceUrl);
                    if (false === $segmentExists) {
                        $analyticsService->segmentAdd($matomoRollupId, $segment, $source->sourceUrl);
                    }

                    $countries = $analyticsService->getCountryBySegment($matomoRollupId, $days, $segment);
                    $countriesSegmented = [];
                    $device_days_countries = [];

                    foreach ($countries as $country) {
                        if (!isset($country[0])) {
                            continue;
                        }

                        $segmentName = $country[0]['code'] . '-' . $source->sourceUrl;
                        $segmentCountry = $country[0]['segment'] . ';' . $segment;

                        if (!in_array($segmentCountry, $countriesSegmented)) {
                            array_push($countriesSegmented, $segmentCountry);

                            $segmentExists = array_search($segmentName, array_column($definedSegments, 'name'));
                            if (false === $segmentExists) {
                                $analyticsService->segmentAdd($matomoRollupId, $segmentCountry, $source->sourceUrl);
                            }

                            $device_days_countries[$country[0]['code']] = $analyticsService->getDeviceBySegment($matomoRollupId, $days, $segmentCountry);
                        }
                    }

                    $nbVisits = [];
                    foreach ($device_days_countries as $country => $device_days) {
                        foreach ($device_days as $report_device) {
                            foreach ($report_device as $device) {
                                if (!isset($nbVisits[$device['label']])) {
                                    $element = new stdClass();
                                    $element->nbVisits = $device['nb_visits'];
                                    $element->originatingCountry = strtoupper($country);
                                    $element->deviceType = $this->getValidDeviceTypeLabel($device['label']);
                                    $nbVisits[$device['label']] = $element;
                                } else {
                                    $nbVisits[$device['label']]->nbVisits += $device['nb_visits'];
                                }
                            }
                        }
                    }

                    foreach ($nbVisits as $visit) {
                        array_push($source->statistics, $visit);
                    }
                    array_push($data->sources, $source);
                }
            }

            $data->nbEntries = count($data->sources);
        } catch (BindingResolutionException $exception) {
            report($exception);

            return [
                'failed' => [
                    'reason' => 'SDG - Unable to bind to Analytics Service',
                ],
            ];
        } catch (AnalyticsServiceException $exception) {
            report($exception);

            return [
                'failed' => [
                    'reason' => 'SDG - Unable to contact the Analytics Service',
                ],
            ];
        } catch (CommandErrorException $exception) {
            report($exception);

            return [
                'failed' => [
                    'reason' => 'SDG -  Invalid command for Analytics Service',
                    'message' => $exception->getMessage(),
                ],
            ];
        }

        return $data;
    }

    protected function getUrlsFromConfig($name)
    {
        return array_map('str_getcsv', file(Storage::disk('persistent')->path('sdg/urls.csv')));
    }

    /**
     * Return validated string for device type.
     *
     * @param string $type the device type
     *
     * @return array the validated device type
     */
    private function getValidDeviceTypeLabel($type): string
    {
        /*
        * Valid values for device type: PC, Tablet, Smartphone, Others
        *
        * Values from Matomo: desktop, smartphone, tablet, feature phone, console, tv, car browser, smart display,
        * camera, portable media player, phablet, smart speaker, wearable
        */

        switch (strtolower($type)) {
            case 'desktop':
                return 'PC';
            case 'smartphone':
                return 'Smartphone';
            case 'tablet':
                return 'Tablet';
            default:
                return 'Others';
        }
    }
}
