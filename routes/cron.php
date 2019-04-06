<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CronJobs Routes
|--------------------------------------------------------------------------
|
| Defined routes:
| - update IPA request route.
| - Check pending websites request route.
*/

/*
 * Update IPA request route.
 */
Route::get('/updateipa', [
    'as' => 'cron-update_ipa',
    'uses' => 'Cron\CronController@updateIPA',
]);

/*
 * Check pending websites request route.
 */
Route::get('/checkwebsites', [
    'as' => 'cron-check_websites',
    'uses' => 'Cron\CronController@checkPendingWebsites',
]);
