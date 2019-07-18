<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * User roles.
 */
final class UserRole extends Enum implements LocalizedEnum
{
    /**
     * Super-admin user role constant.
     */
    public const SUPER_ADMIN = 'super-admin';

    /**
     * Admin user role constant.
     */
    public const ADMIN = 'admin';

    /**
     * Registered user role constant.
     */
    public const DELEGATED = 'delegated';

    /**
     * Registered user role constant.
     */
    public const REGISTERED = 'registered';

    /**
     * Removed user role constant.
     */
    public const REMOVED = 'removed';
}
