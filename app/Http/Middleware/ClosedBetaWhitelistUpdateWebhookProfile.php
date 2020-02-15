<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

/**
 * Web hook profile to control job dispatching for a web hook call.
 */
class ClosedBetaWhitelistUpdateWebhookProfile implements WebhookProfile
{
    /**
     * Get if the job should be dispatched.
     *
     * @param Request $request the incoming request
     *
     * @return bool true to enable job dispatching, false otherwise
     */
    public function shouldProcess(Request $request): bool
    {
        return config('wai.closed_beta')
            && 'push' === $request->header('X-GitHub-Event', '')
            && config('webhook-client.configs.0.repository.branch') === Str::afterLast(json_decode($request->getContent())->ref, '/');
    }
}
