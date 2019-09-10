<?php

namespace App\Enums\Logs;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Job types.
 */
class JobType extends Enum implements LocalizedEnum
{
    /**
     * Password tokens clearing job.
     */
    public const CLEAR_PASSWORD_TOKEN = 0;

    /**
     * IPA update job.
     */
    public const UPDATE_PA_FROM_IPA = 1;

    /**
     * Password reset token sending job.
     */
    public const SEND_RESET_PASSWORD_TOKEN = 2;

    /**
     * Email verification token sending job.
     */
    public const SEND_EMAIL_VERIFICATION_TOKEN = 3;

    /**
     * Process pending websites job.
     */
    public const PROCESS_PENDING_WEBSITES = 4;

    /**
     * Process users index.
     */
    public const PROCESS_USERS_INDEX = 5;

    /**
     * Process websites index.
     */
    public const PROCESS_WEBSITES_INDEX = 6;

    /**
     * Process websites monitoring.
     */
    public const MONITOR_WEBSITES_TRACKING = 7;
}
