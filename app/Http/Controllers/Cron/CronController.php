<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessIPAList;
use App\Jobs\ProcessPendingWebsites;
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

        return response()->json(['message' => 'Check submitted'], 202);
    }
}
