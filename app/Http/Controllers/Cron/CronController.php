<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Jobs\MonitorWebsitesTracking;
use App\Jobs\ProcessPendingWebsites;
use App\Jobs\ProcessPublicAdministrationsUpdateFromIpa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CronJobs submission controller.
 */
class CronController extends Controller
{
    /**
     * Update IPA request.
     *
     * @return \Illuminate\Http\JsonResponse JSON response with job submission status
     */
    public function updateFromIpa(): JsonResponse
    {
        dispatch(new ProcessPublicAdministrationsUpdateFromIpa());

        return response()->json(['message' => 'Update submitted'], 202);
    }

    /**
     * Check pending websites request.
     *
     * @param Request $request the incoming request
     *
     * @return \Illuminate\Http\JsonResponse JSON response with the job submission status
     */
    public function checkPendingWebsites(Request $request): JsonResponse
    {
        dispatch(new ProcessPendingWebsites($request->input('purge') ?? false));

        return response()->json(['message' => 'Pending check submitted'], 202);
    }

    /**
     * Monitor active websites activity request.
     *
     * @return \Illuminate\Http\JsonResponse JSON response with the job submission status
     */
    public function monitorWebsitesActivity(): JsonResponse
    {
        dispatch(new MonitorWebsitesTracking());

        return response()->json(['message' => 'Activity check submitted'], 202);
    }
}
