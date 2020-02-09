<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class ClosedBetaWhitelistUpdateWebhookProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        return config('wai.closed_beta');
    }
}
