<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * User status.
 */
final class UserStatus extends Enum implements LocalizedEnum
{
    /**
     * User status invited constant.
     */
    public const INVITED = 0;

    /**
     * User status inactive constant.
     */
    public const INACTIVE = 1;

    /**
     * User status pending constant.
     */
    public const PENDING = 2;

    /**
     * User status active constant.
     */
    public const ACTIVE = 3;

    /**
     * User status suspended constant.
     */
    public const SUSPENDED = 4;
}
