<?php

namespace App\Enums\Logs;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * Exception types.
 */
class ExceptionType extends Enum implements LocalizedEnum
{
    /**
     * Generic exception.
     */
    public const GENERIC = 0;

    /**
     * Analytics Service account related exception.
     */
    public const ANALYTICS_ACCOUNT = 1;

    /**
     * Analytics Service related exception.
     */
    public const ANALYTICS_SERVICE = 2;

    /**
     * Analytics Service command related exception.
     */
    public const ANALYTICS_COMMAND = 3;

    /**
     * Client HTTP error.
     */
    public const HTTP_CLIENT_ERROR = 4;

    /**
     * Internal server error.
     */
    public const SERVER_ERROR = 5;

    /**
     * Tenant selection exception.
     */
    public const TENANT_SELECTION = 6;

    /**
     * IPA search exception.
     */
    public const IPA_INDEX_SEARCH = 7;

    /**
     * Websites index search exception.
     */
    public const WEBSITE_INDEX_SEARCH = 8;

    /**
     * Users index search exception.
     */
    public const USER_INDEX_SEARCH = 9;

    /**
     * Invalid website status related exception.
     */
    public const INVALID_WEBSITE_STATUS = 10;

    /**
     * Invalid operation related exception.
     */
    public const INVALID_OPERATION = 11;
}
