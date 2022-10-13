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
    | Primary website not tracking weekly notification
    |--------------------------------------------------------------------------
    |
    | This value is used to configure the day of the week (from '1', Monday, to
    | '7', Sunday) the system sends notification for primary website not tracking
    | after 'archive website expire' threshold has passed.
    |
    */

    'primary_website_not_tracking_notification_day' => env('PRIMARY_WEBSITE_NOT_TRACKING_NOTIFICATION_WEEK_DAY', 1),

    /*
    |--------------------------------------------------------------------------
    | Archive website warning notification interval
    |--------------------------------------------------------------------------
    |
    | This value is used to configure interval between notifications the
    | system sends for scheduled website archiving when more than
    | 'archive_warning_daily_notification' days left.
    |
    */

    'archive_warning_notification_interval' => env('ARCHIVING_WEBSITE_NOTIFICATION_INTERVAL', 5),

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

    /*
     |--------------------------------------------------------------------------
     | Disable adding new websites
     |--------------------------------------------------------------------------
     |
     | This configuration is used to prevent users from adding new websites.
     |
     */

    'app_suspended' => env('APP_SUSPENDED', false),

    /*
     |--------------------------------------------------------------------------
     | Enable closed beta
     |--------------------------------------------------------------------------
     |
     | This value is used to enable closed beta; when active, only public
     | administrations included in the closed beta whitelist can be
     | registered in the system.
     |
     */

    'closed_beta' => env('APP_CLOSED_BETA_ENABLED', false),

    /*
     |--------------------------------------------------------------------------
     | Enable custom public administrations
     |--------------------------------------------------------------------------
     |
     | This value is used to enable custom public administrations support; when
     | active, users can create their own public administration and register
     | it into the portal.
     |
     */

    'custom_public_administrations' => env('APP_CUSTOM_PUBLIC_ADMINISTRATIONS_ENABLED', false),

    /*
     |--------------------------------------------------------------------------
     | Set day, hour and minute to reset public playground
     |--------------------------------------------------------------------------
     |
     | This values are used to set day, hour and minute to reset public playground;
     | Day: sundays = 0, mondays = 1, tuesdays = 2, wednesdays = 3, thursdays = 4, fridays = 5, saturdays = 6
     */

    'reset_public_playground_day_verbose' => env('RESET_PUBLIC_PLAYGROUND_DAY_VERBOSE', 'sunday'),
    'reset_public_playground_day' => env('RESET_PUBLIC_PLAYGROUND_DAY', 0),
    'reset_public_playground_hour' => env('RESET_PUBLIC_PLAYGROUND_HOUR', 23),
    'reset_public_playground_minute' => env('RESET_PUBLIC_PLAYGROUND_MINUTE', 30),
];
