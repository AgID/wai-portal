<?php

namespace App\Http\Controllers\Logs;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogFilteringRequest;
use App\Services\ElasticSearchService;
use Carbon\Carbon;
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
    /**
     * Display the logs list.
     *
     * @param Request $request the request
     *
     * @return View the view to show
     */
    public function show(Request $request): View
    {
        $currentUser = $request->user();
        $searchWebsitesEndpoint = $currentUser->isAn(UserRole::SUPER_ADMIN)
            ? route('admin.logs.websites.search')
            : route('logs.websites.search');
        $searchUsersEndpoint = $currentUser->isAn(UserRole::SUPER_ADMIN)
        ? route('admin.logs.users.search')
        : route('logs.users.search');
        $logsDatatable = [
            'datatableOptions' => [
                'processing' => true,
                'serverSide' => true,
                'textWrap' => true,
            ],
            'columns' => [
                [
                    'data' => 'datetime',
                    'name' => __('data e ora'),
                    'searchable' => false,
                    'className' => 'text-nowrap text-center pr-2',
                ],
                [
                    'data' => 'level_name',
                    'name' => __('livello'),
                    'orderable' => false,
                    'searchable' => false,
                    'className' => 'text-nowrap pr-2',
                ],
                [
                    'data' => 'message',
                    'name' => __('messaggio'),
                    'orderable' => false,
                    'searchable' => false,
                ],
            ],
            'source' => $currentUser->isA(UserRole::SUPER_ADMIN) ? route('admin.logs.data') : route('logs.data'),
            'caption' => __('messaggi di log di :app', ['app' => config('app.name')]),
            'columnsOrder' => [['datetime', 'desc']],
        ];

        return view('pages.logs.show')->with(compact('currentUser', 'searchWebsitesEndpoint', 'searchUsersEndpoint'))->with($logsDatatable);
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
            $client = app(ElasticSearchService::class)->getClient();

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
                $response['data'][$index]['level_name'] = [
                    'display' => '<span class="badge log-level ' . strtolower($response['data'][$index]['level_name']) . '">' . $response['data'][$index]['level_name'] . '</span>',
                    'raw' => $response['data'][$index]['level_name'],
                ];
            }
        } catch (Exception $exception) {
            report($exception);
            $response['errors'] = __('Si è verificato un errore nel recupero dei log.');
            $responseCode = 500;
        }

        return response()->json($response, $responseCode ?? 200);
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
        $user = auth()->user();
        $params['message'] = empty($data['message']) ? null : $data['message'];
        $params['from'] = empty($data['start']) ? 0 : $data['start'];
        $params['size'] = empty($data['length']) ? 10 : $data['length'];

        if (!empty($data['severity'])) {
            $value = (int) $data['severity'];
            if ($value < Logger::INFO && !$user->isA(UserRole::SUPER_ADMIN)) {
                $value = Logger::INFO;
            }
            $params['severity'] = $value;
        }

        if (!empty($data['start_time'])) {
            $params['start_time'] = Carbon::createFromFormat('d/m/Y H:i', $data['start_date'] . ' ' . $data['start_time'])->toIso8601String();
        }

        if (!empty($data['end_time'])) {
            $params['end_time'] = Carbon::createFromFormat('d/m/Y H:i', $data['end_date'] . ' ' . $data['end_time'])->toIso8601String();
        }

        if (!empty($data['order']) && !empty($data['order'][0]) && !empty($data['order'][0]['dir'])) {
            $params['order'] = $data['order'][0]['dir'];
        }

        $params['filters'][] = ['term' => ['channel' => config('app.env')]];

        if ($user->isA(UserRole::SUPER_ADMIN)) {
            if (!empty($data['ipa_code'])) {
                $params['filters'][] = ['term' => ['context.pa' => $data['ipa_code']]];
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
        if (isset($data['exception_type'])) {
            $params['filters'][] = ['term' => ['context.exception_type' => $data['exception_type']]];
        }

        return $params ?? [];
    }
}
