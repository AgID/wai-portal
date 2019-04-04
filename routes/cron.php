<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CronJobs Routes
|--------------------------------------------------------------------------
|
| Defined routes:
| - update IPA request route.
|
*/

/*
 * Update IPA request route.
 */
Route::get('/updateipa', [
    'as' => 'cron-update_ipa',
    'uses' => 'Cron\CronController@updateIPA',
]);
