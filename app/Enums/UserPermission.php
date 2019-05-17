<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * User permissions.
 */
final class UserPermission extends Enum implements LocalizedEnum
{
    /**
     * Access admin area permission constant.
     */
    public const ACCESS_ADMIN_AREA = 'access-admin-area';

    /**
     * Manage users permission constant.
     */
    public const MANAGE_USERS = 'manage-users';

    /**
     * Manage websites permission constant.
     */
    public const MANAGE_WEBSITES = 'manage-websites';

    public const VIEW_LOGS = 'view-logs';

    /**
     * Manage analytics data permission constant.
     */
    public const MANAGE_ANALYTICS = 'manage-analytics';

    /**
     * Read analytics data permission constant.
     */
    public const READ_ANALYTICS = 'read-analytics';

    /**
     * No access to analytics data permission constant.
     */
    public const NO_ACCESS = 'no-access';

    /**
     * Do nothing permission constant.
     */
    public const DO_NOTHING = 'do-nothing';
}
