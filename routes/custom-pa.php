<?php

use Illuminate\Support\Facades\Route;

Route::middleware('spid.auth', 'auth', 'verified:verification.notice')->group(function () {
    Route::prefix('/websites')->group(function () {
        Route::get('/custom', 'CustomPublicAdministrationController@index')
            ->name('websites.create.primary.custom');

        Route::post('/store-custom-primary', 'CustomPublicAdministrationController@store')
            ->name('websites.store.primary.custom');
    });
});
