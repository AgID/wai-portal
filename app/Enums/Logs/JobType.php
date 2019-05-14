<?php

namespace App\Enums\Logs;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

class JobType extends Enum implements LocalizedEnum
{
    public const CLEAR_PASSWORD_TOKEN = 0;

    public const UPDATE_IPA = 1;

    public const SEND_RESET_PASSWORD_TOKEN = 2;

    public const SEND_EMAIL_VERIFICATION_TOKEN = 3;
}
