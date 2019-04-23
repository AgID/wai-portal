<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessIPAList;
use App\Jobs\ProcessPendingWebsites;
use App\Jobs\ProcessWebsitesMonitoring;
use Illuminate\Http\JsonResponse;

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
    public function updateIPA(): JsonResponse
    {
        dispatch(new ProcessIPAList());

        return response()->json(['message' => 'Update submitted'], 202);
    }

    /**
     * Check pending websites request.
     *
     * @return \Illuminate\Http\JsonResponse JSON response with the job submission status
     */
    public function checkPendingWebsites(): JsonResponse
    {
        dispatch(new ProcessPendingWebsites());

        return response()->json(['message' => 'Pending check submitted'], 202);
    }

    /**
     * Monitor active websites activity request.
     *
     * @return \Illuminate\Http\JsonResponse JSON response with the job submission status
     */
    public function monitorWebsitesActivity(): JsonResponse
    {
        dispatch(new ProcessWebsitesMonitoring());

        return response()->json(['message' => 'Activity check submitted'], 202);
    }
}
