<?php

namespace App\Jobs;

use App\Events\Jobs\ClosedBetaWhitelistUpdateFailed;
use App\Traits\ManageClosedBetaWhitelist;
use Illuminate\Support\Facades\Cache;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\ProcessWebhookJob;
use Throwable;

/**
 * Received closed beta whitelist web hook calls job.
 */
class UpdateClosedBetaWhitelist extends ProcessWebhookJob
{
    use ManageClosedBetaWhitelist;

    /**
     * Ipa codes of whitelisted public administrations cache key.
     */
    public const CLOSED_BETA_WHITELIST_KEY = 'closed-beta-whitelist';

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
     *
     * @param WebhookCall|null $webhookCall
     */
    public function __construct(?WebhookCall $webhookCall = null)
    {
        //NOTE: no parent constructor call since
        //      database persistence is not wanted
        //      and no migration has been scheduled
        $this->payload = $webhookCall->payload ?? null;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Cache::forever(self::CLOSED_BETA_WHITELIST_KEY, $this->download($this->payload));
        } catch (Throwable $exception) {
            event(new ClosedBetaWhitelistUpdateFailed($exception->getCode(), $exception->getMessage()));
        }
    }
}
