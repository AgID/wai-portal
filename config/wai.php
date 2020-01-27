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
    | This value is used to configure the time window (in days) the system
    | checks for tracking activity into Analytics Service reports. If there are no
    | visits in this interval, the system sends a notification to warn
    | for scheduled archiving.
    |
    */

    'archive_warning' => env('ARCHIVING_WEBSITE_WARNING', 10),

    /*
    |--------------------------------------------------------------------------
    | Archive website warning daily notification
    |--------------------------------------------------------------------------
    |
    | This value is used to configure the time interval (in days) the system
    | starts sending daily notification to warn for scheduled website archiving.
    |
    */

    'archive_warning_daily_notification' => env('ARCHIVING_WEBSITE_DAILY_NOTIFICATION', 3),

    /*
    |--------------------------------------------------------------------------
    | Archive website warning weekly notification
    |--------------------------------------------------------------------------
    |
    | This value is used to configure the day of the week (from '0', Sunday, to
    | '6', Saturday)the system sends notification for scheduled website
    | archiving when more than 'archive_warning_daily_notification' days left.
    |
    */

    'archive_warning_notification_day' => env('ARCHIVING_WEBSITE_NOTIFICATION_WEEK_DAY', 1),

    /*
    |--------------------------------------------------------------------------
    | Archive website expire
    |--------------------------------------------------------------------------
    |
    | This values is used to configure the time window (in days) the system
    | checks for tracking activity into Analytics Service reports. When this
    | limit is reached, the system archive the website.
    |
    */

    'archive_expire' => env('ARCHIVING_WEBSITE_ARCHIVE', 20),

];
