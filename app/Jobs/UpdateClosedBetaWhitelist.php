<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\ProcessWebhookJob;
use Symfony\Component\Yaml\Yaml;

/**
 * Received closed beta whitelist web hook calls job.
 */
class UpdateClosedBetaWhitelist extends ProcessWebhookJob
{
    /**
     * Ipa codes of whitelisted public administrations cache key.
     */
    public const CLOSED_BETA_WHITELIST_KEY = 'closed-beta-whitelist';

    /**
     * Ipa codes of whitelisted public administrations file name.
     */
    public const CLOSED_BETA_WHITELIST_FILENAME = 'closed_beta_whitelist.yml';

    //NOTE: DON'T USE $webhookCall field because it's not initialized!

    /**
     * Whitelisted ipa codes array.
     *
     * @var array the array
     */
    private $payload;

    /**
     * Default constructor.
     * NOTE: no parent constructor call since no persistence needed/supported.
     *
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(WebhookCall $webhookCall)
    {
        //NOTE: no parent constructor call since
        //      database persistence is not wanted
        //      and no migration has been scheduled
        $this->payload = $webhookCall->payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::forever(self::CLOSED_BETA_WHITELIST_KEY, collect($this->payload));
        Storage::put(self::CLOSED_BETA_WHITELIST_FILENAME, Yaml::dump($this->payload));
    }
}
