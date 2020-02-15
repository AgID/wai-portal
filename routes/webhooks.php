<?php

/*
|--------------------------------------------------------------------------
| Web Hooks Routes
|--------------------------------------------------------------------------
|
| Routes registered for incoming web-hooks.
|
*/

use Illuminate\Support\Facades\Route;

Route::webhooks('whitelist-update', 'closed-beta-whitelist');
