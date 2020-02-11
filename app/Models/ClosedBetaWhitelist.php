<?php

namespace App\Models;

use Exception;
use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;
use Symfony\Component\Yaml\Yaml;

/**
 * Closed beta whitelist web hook call model.
 */
class ClosedBetaWhitelist extends WebhookCall
{
    /**
     * Store the request.
     * NOTE: model are not persisted into database.
     *
     * @param WebhookConfig $config the web hook configuration
     * @param Request $request the incoming request
     *
     * @return WebhookCall the model
     */
    public static function storeWebhook(WebhookConfig $config, Request $request): WebhookCall
    {
        return self::make([
            'name' => $config->name,
            'payload' => Yaml::parse($request->getContent()),
        ]);
    }

    /**
     * Save a web hook call processing exception.
     * NOTE: field is not used, nothing to do; this method has been
     *       overridden to avoid database saving.
     *
     * @param Exception $exception the exception
     *
     * @return ClosedBetaWhitelist the model instance
     */
    public function saveException(Exception $exception): ClosedBetaWhitelist
    {
        return $this;
    }

    /**
     * Clear a previous web hook call processing exception.
     * NOTE: field is not used, nothing to do; this method has been
     *       overridden to avoid database saving.
     *
     * @return ClosedBetaWhitelist the model instance
     */
    public function clearException(): ClosedBetaWhitelist
    {
        return $this;
    }
}
