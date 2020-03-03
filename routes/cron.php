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
| - Check websites activity request route.
*/

/*
 * Update IPA request route.
 */
Route::get('/update-from-ipa', 'Cron\CronController@updateFromIpa')
    ->name('cron.ipa.update');

/*
 * Check pending websites request route.
 */
Route::get('/check-pending-websites', 'Cron\CronController@checkPendingWebsites')
    ->name('cron.websites.checkpending');

/*
 * Check websites activity request route.
 */
Route::get('/monitor-websites', 'Cron\CronController@monitorWebsitesActivity')
    ->name('cron.websites.monitor');
