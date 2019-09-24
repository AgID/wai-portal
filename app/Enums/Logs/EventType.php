<?php

namespace App\Enums\Logs;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Event types.
 */
class EventType extends Enum implements LocalizedEnum
{
    /**
     * Application exception event.
     */
    public const EXCEPTION = 0;

    /**
     * Analytics Service login event.
     */
    public const ANALYTICS_LOGIN = 1;

    /**
     * Pending websites check completed event.
     */
    public const PENDING_WEBSITES_CHECK_COMPLETED = 2;

    /**
     * Websites tracking check completed event.
     */
    public const TRACKING_WEBSITES_CHECK_COMPLETED = 3;

    /**
     * Public administrations update from IPA index completed event.
     */
    public const UPDATE_PA_FROM_IPA_COMPLETED = 4;

    /**
     * New public administration registered event.
     */
    public const PUBLIC_ADMINISTRATION_REGISTERED = 5;

    /**
     * Public administration activated event.
     */
    public const PUBLIC_ADMINISTRATION_ACTIVATED = 6;

    /**
     * Public administration activation error event.
     */
    public const PUBLIC_ADMINISTRATION_ACTIVATION_FAILED = 7;

    /**
     * Public administration updated event.
     */
    public const PUBLIC_ADMINISTRATION_UPDATED = 8;

    /**
     * Public administration primary website changed event.
     */
    public const PUBLIC_ADMINISTRATION_PRIMARY_WEBSITE_CHANGED = 9;

    /**
     * Public administration removed event.
     */
    public const PUBLIC_ADMINISTRATION_PURGED = 10;

    /**
     * User login event.
     */
    public const USER_LOGIN = 11;

    /**
     * User logout event.
     */
    public const USER_LOGOUT = 12;

    /**
     * New user registered event.
     */
    public const USER_REGISTERED = 13;

    /**
     * New user invited event.
     */
    public const USER_INVITED = 14;

    /**
     * User email verified event.
     */
    public const USER_VERIFIED = 15;

    /**
     * User activated event.
     */
    public const USER_ACTIVATED = 16;

    /**
     * User email address changed event.
     */
    public const USER_EMAIL_CHANGED = 17;

    /**
     * User status changed event.
     */
    public const USER_STATUS_CHANGED = 18;

    /**
     * User removed event.
     */
    public const USER_DELETED = 19;

    /**
     * User restored event.
     */
    public const USER_RESTORED = 20;

    /**
     * User website access changed event.
     */
    public const USER_WEBSITE_ACCESS_CHANGED = 21;

    /**
     * New website registered event.
     */
    public const WEBSITE_ADDED = 22;

    /**
     * Website URL change event.
     */
    public const WEBSITE_URL_CHANGED = 23;

    /**
     * Website activated event.
     */
    public const WEBSITE_ACTIVATED = 24;

    /**
     * Website status changed event.
     */
    public const WEBSITE_STATUS_CHANGED = 25;

    /**
     * Website scheduled for archiving event.
     */
    public const WEBSITE_ARCHIVING = 26;

    /**
     * Website archived event.
     */
    public const WEBSITE_ARCHIVED = 27;

    /**
     * Website unarchived event.
     */
    public const WEBSITE_UNARCHIVED = 28;

    /**
     * Website scheduled for removing event.
     */
    public const WEBSITE_PURGING = 29;

    /**
     * Website removed event.
     */
    public const WEBSITE_PURGED = 30;

    /**
     * Website manually deleted event.
     */
    public const WEBSITE_DELETED = 31;

    /**
     * Website restored event.
     */
    public const WEBSITE_RESTORED = 32;

    /**
     * Primary website tracking failing event.
     */
    public const PRIMARY_WEBSITE_NOT_TRACKING = 33;

    /**
     * Users index updated event.
     */
    public const USERS_INDEXING_COMPLETED = 34;

    /**
     * Websites index updated event.
     */
    public const WEBSITES_INDEXING_COMPLETED = 35;

    /**
     * Mail sent event.
     */
    public const MAIL_SENT = 99;
}
