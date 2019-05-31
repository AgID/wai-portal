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
     * I.P.A. update job.
     */
    public const UPDATE_IPA = 1;

    /**
     * Password reset token sending job.
     */
    public const SEND_RESET_PASSWORD_TOKEN = 2;

    /**
     * Email verification token sending job.
     */
    public const SEND_EMAIL_VERIFICATION_TOKEN = 3;
}
