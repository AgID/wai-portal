<?php

use Illuminate\Support\Facades\Route;

Route::middleware('spid.auth', 'auth', 'verified:verification.notice')->group(function () {
    Route::prefix('/websites')->group(function () {
        Route::get('/custom', 'WebsiteController@custom')
            ->name('websites.create.primary.custom');
    });
});
