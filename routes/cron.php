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
    'as' => 'cron.ipa.update',
    'uses' => 'Cron\CronController@updateIPA',
]);

/*
 * Check pending websites request route.
 */
Route::get('/checkpendingwebsites', [
    'as' => 'cron.websites.checkpending',
    'uses' => 'Cron\CronController@checkPendingWebsites',
]);
