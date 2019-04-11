<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pending website warning
    |--------------------------------------------------------------------------
    |
    | This values is used to configure the time limit (in days) the system waits
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
    | This values is used to configure the time limit (in days) the system
    | waits for website activation. When this limit is reached, the system
    | completely removes the website from the portal.
    |
    */

    'purge_expiry' => env('PENDING_WEBSITE_REMOVE', 15),

];
