<?php

namespace App\Traits;

use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Ramsey\Uuid\Uuid;
use stdClass;

/**
 * Get the dataset to Single Digital Gateway API for the Statistics on Information Services.
 */
trait BuildDatasetForSingleDigitalGatewayAPI
{
    public function getUrlsFromConfig($name)
    {
        return json_decode(file_get_contents(resource_path('data/' . $name . '.json')), true);
    }

    /**
     * Get the dataset to Single Digital Gateway API for the Statistics on Information Services.
     *
     * @return array the dataset
     */
    public function buildDatasetForSDG()
    {
        $urls = $this->getUrlsFromConfig('urls');

        $analyticsService = app()->make('analytics-service');
        $sDGService = app()->make('single-digital-gateway-service');

        $days = config('single-digital-gateway-service.last_days');
        $idSite = config('analytics-service.public_dashboard');

        date_default_timezone_set('UTC');
        $referencePeriod = new stdClass();
        $referencePeriod->startDate = date('Y-m-d\TH:i:s\Z', strtotime('-' . ($days - 1) . ' days'));
        $referencePeriod->endDate = date('Y-m-d\TH:i:s\Z');

        $data = new stdClass();
        $data->uniqueId = $sDGService->getUniqueID();
        $data->referencePeriod = $referencePeriod;
        $data->transferDate = date('Y-m-d\TH:i:s\Z');
        $data->transferType = 'API';
        $data->nbEntries = 0;
        $data->sources = [];

        try {
            $definedSegments = $analyticsService->getAllSegments();

            foreach ($urls['domains'] as $url) {
                $source = new stdClass();
                $source->sourceUrl = $url;
                $source->statistics = [];

                $segmentExists = array_search($url, array_column($definedSegments, 'name'));

                // creo il segment per la url

                $segment = urlencode('pageUrl==' . $url);
                if (false === $segmentExists) {
                    $analyticsService->segmentAdd($idSite, $segment, $url);
                }

                $countries = $analyticsService->getCountryBySegment($idSite, $days, $segment);
                $countriesSegmented = [];
                $device_days_countries = [];

                foreach ($countries as $country) {
                    if (!isset($country[0])) {
                        continue;
                    }

                    $segmentName = $country[0]['code'] . '-' . $url;
                    $segmentCountry = $country[0]['segment'] . ';' . $segment;

                    if (!in_array($segmentCountry, $countriesSegmented)) {
                        array_push($countriesSegmented, $segmentCountry);

                        $segmentExists = array_search($segmentName, array_column($definedSegments, 'name'));
                        if (false === $segmentExists) {
                            $analyticsService->segmentAdd($idSite, $segmentCountry, $url);
                        }

                        $device_days_countries[$country[0]['code']] = $analyticsService->getDeviceBySegment($idSite, $days, $segmentCountry);
                    }
                }

                $nbvisits = [];

                foreach ($device_days_countries as $country => $device_days) {
                    foreach ($device_days as $report_device) {
                        foreach ($report_device as $device) {
                            if (!isset($nbvisits[$device['label']])) {
                                $element = new stdClass();
                                $element->nbVisits = $device['nb_visits'];
                                $element->originatingCountry = strtoupper($country);
                                $element->deviceType = $device['label'];
                                $nbvisits[$device['label']] = $element;
                            } else {
                                $nbvisits[$device['label']]->nbVisits += $device['nb_visits'];
                            }
                        }
                    }
                }

                foreach ($nbvisits as $visit) {
                    array_push($source->statistics, $visit);
                }
                array_push($data->sources, $source);
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
                ],
            ];
        }

        return $data;
    }
}
