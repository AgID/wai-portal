<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pending website warning
    |--------------------------------------------------------------------------
    |
    | This value is used to configure the time limit (in days) the system waits
    | for website activation. When this limit is reached, the system sends a
    | notification to warn for scheduled removal.
    |
    */

    'purge_warning' => env('PENDING_WEBSITE_WARNING', 10),

    /*
    |--------------------------------------------------------------------------
    | Pending website termination
    |--------------------------------------------------------------------------
    |
    | This value is used to configure the time limit (in days) the system
    | waits for website activation. When this limit is reached, the system
    | completely removes the website from the portal.
    |
    */

    'purge_expiry' => env('PENDING_WEBSITE_REMOVE', 15),

    /*
    |--------------------------------------------------------------------------
    | Archive website warning
    |--------------------------------------------------------------------------
    |
    | This value is used to configure the time window (in months) the system
    | checks for tracking activity into Analytics Service reports. If there are no
    | visits in this interval, the system sends a notification to warn
    | for scheduled archiving.
    |
    */
    'archive_warning' => env('ARCHIVING_WEBSITE_WARNING', 2),

    /*
    |--------------------------------------------------------------------------
    | Archive website termination
    |--------------------------------------------------------------------------
    |
    | This values is used to configure the time window (in months) the system
    | checks for tracking activity into Analytics Service reports. When this
    | limit is reached, the system archive the website.
    |
    */
    'archive_expiry' => env('ARCHIVING_WEBSITE_ARCHIVE', 3),

];
