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
Route::get('/updateipa', 'Cron\CronController@updateIpa')
    ->name('cron.ipa.update');

/*
 * Check pending websites request route.
 */
Route::get('/checkpendingwebsites', 'Cron\CronController@checkPendingWebsites')
    ->name('cron.websites.checkpending');

/*
 * Check websites activity request route.
 */
Route::get('/monitorwebsites', 'Cron\CronController@monitorWebsitesActivity')
    ->name('cron.websites.monitor');
