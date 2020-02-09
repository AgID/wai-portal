<?php

namespace App\Models;

use Exception;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Symfony\Component\Yaml\Yaml;

class ClosedBetaWhitelist extends WebhookCall
{
    public static function storeWebhook(WebhookConfig $config, Request $request): WebhookCall
    {
        return self::make([
            'name' => $config->name,
            'payload' => Yaml::parse($request->getContent()),
        ]);
    }

    public function saveException(Exception $exception)
    {
        return $this;
    }

    public function clearException()
    {
        return $this;
    }
}
