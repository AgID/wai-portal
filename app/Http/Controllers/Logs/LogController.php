<?php

namespace App\Http\Controllers\Logs;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogFilteringRequest;
use App\Traits\InteractsWithUserIndex;
use App\Traits\InteractsWithWebsiteIndex;
use Carbon\Carbon;
use Elasticsearch\ClientBuilder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Monolog\Logger;

/**
 * Logs visualization controller.
 */
class LogController extends Controller
{
    use InteractsWithWebsiteIndex;
    use InteractsWithUserIndex;

    /**
     * Display the logs list.
     *
     * @param Request $request the request
     *
     * @return View the view to show
     */
    public function show(Request $request): View
    {
        $data = [
            'currentUser' => $request->user(),
            'currentDate' => Carbon::now()->format('d/m/Y'),
            'startTime' => Carbon::now()->subMinutes(15)->format('H:i'),
            'endTime' => Carbon::now()->format('H:i'),
            'columns' => [
                ['data' => 'datetime', 'name' => __('ui.pages.logs.table.headers.time'), 'className' => 'u-textNoWrap', 'searchable' => false],
                ['data' => 'level_name', 'name' => __('ui.pages.logs.table.headers.level'), 'orderable' => false, 'searchable' => false, 'className' => 'u-textNoWrap dt-body-center'],
                ['data' => 'message', 'name' => __('ui.pages.logs.table.headers.message'), 'orderable' => false, 'searchable' => false],
            ],
            'source' => $request->user()->isA(UserRole::SUPER_ADMIN) ? route('admin.logs.data') : route('logs.data'),
            'caption' => __('ui.pages.logs.table.caption'),
            'columnsOrder' => [['datetime', 'desc']],
        ];

        return view('pages.logs.show')->with($data);
    }

    /**
     * Retrieve the logs data.
     *
     * @param LogFilteringRequest $request the request
     *
     * @return JsonResponse the JSON response
     */
    public function data(LogFilteringRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $response = [
            'draw' => (int) $validatedData['draw'],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ];

        $data = $this->extractData($validatedData);

        try {
            $client = ClientBuilder::create()
                ->setHosts([config('elastic-search.host') . ':' . config('elastic-search.port')])
                ->build();

            $results = $client->searchTemplate([
                'index' => config('elastic-search.index_name'),
                'body' => [
                    'id' => config('elastic-search.search_template_name'),
                    'params' => [
                        'from' => $data['from'],
                        'size' => $data['size'],
                        'fields' => ['datetime', 'message', 'level_name'],
                        'order' => $data['order'] ?? null,
                        'message' => $data['message'] ?? null,
                        'filters' => $data['filters'] ?? null,
                        'has_ranges' => isset($data['start_time']) || isset($data['end_time']) || isset($data['severity']),
                        'has_time' => isset($data['start_time']) || isset($data['end_time']),
                        'start_time' => $data['start_time'] ?? null,
                        'end_time' => $data['end_time'] ?? null,
                        'severity' => $data['severity'] ?? null,
                    ],
                ],
            ]);

            $response['recordsTotal'] = $results['hits']['total']['value'];
            $response['recordsFiltered'] = $results['hits']['total']['value'];
            $response['data'] = Arr::pluck($results['hits']['hits'], '_source');
            foreach ($response['data'] as $index => $result) {
                $response['data'][$index]['datetime'] = Carbon::parse($result['datetime'])->format('d/m/Y H:i:s');
            }
        } catch (Exception $exception) {
            report($exception);
            $response['error'] = __('ui.pages.500.elasticsearch_description');
        }

        return response()->json($response);
    }

    /**
     * Extract query parameters from a request validated data array.
     *
     * @param array $data the data
     *
     * @return array the query parameters array
     */
    private function extractData(array $data): array
    {
        $params['message'] = empty($data['message']) ? null : $data['message'];

        $params['from'] = empty($data['start']) ? 0 : $data['start'];

        $params['size'] = empty($data['length']) ? 10 : $data['length'];

        if (!empty($data['severity'])) {
            $value = (int) $data['severity'];
            if ($value < Logger::INFO && !auth()->user()->isA(UserRole::SUPER_ADMIN)) {
                $value = Logger::INFO;
            }
            $params['severity'] = $value;
        }

        if (!empty($data['start_time'])) {
            $params['start_time'] = Carbon::createFromFormat('d/m/Y H:i', $data['date'] . ' ' . $data['start_time'])->toIso8601String();
        }

        if (!empty($data['end_time'])) {
            $params['end_time'] = Carbon::createFromFormat('d/m/Y H:i', $data['date'] . ' ' . $data['end_time'])->toIso8601String();
        }

        if (!empty($data['order']) && !empty($data['order'][0]) && !empty($data['order'][0]['dir'])) {
            $params['order'] = $data['order'][0]['dir'];
        }

        $params['filters'][] = ['term' => ['channel' => config('app.env')]];

        if (auth()->user()->isA(UserRole::SUPER_ADMIN)) {
            if (!empty($data['pa_ipa_code'])) {
                $params['filters'][] = ['term' => ['context.pa' => $data['pa_ipa_code']]];
            }
        } else {
            $params['filters'][] = ['term' => ['context.pa' => current_public_administration()->ipa_code]];
        }

        if (!empty($data['website_id'])) {
            $params['filters'][] = ['term' => ['context.website' => $data['website_id']]];
        }
        if (!empty($data['user_uuid'])) {
            $params['filters'][] = ['term' => ['context.user' => $data['user_uuid']]];
        }
        if (isset($data['job'])) {
            $params['filters'][] = ['term' => ['context.job' => $data['job']]];
        }
        if (isset($data['event'])) {
            $params['filters'][] = ['term' => ['context.event' => $data['event']]];
        }
        if (isset($data['exception'])) {
            $params['filters'][] = ['term' => ['context.type' => $data['exception']]];
        }

        return $params ?? [];
    }
}
