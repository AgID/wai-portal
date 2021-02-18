<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('api.auth')->group(function () {
    Route::get('/', function () {
        return response()->json(['test' => getallheaders()], 200);
    });
    Route::prefix('/users')->group(function () {
        Route::get('/', 'UserController@dataApiJson')
            ->name('api.users');
        Route::post('/', 'UserController@storeJson')
            ->name('api.users.store');
        Route::get('/{fn}', 'UserController@showJson')
            ->name('api.users.show');
        Route::patch('/{fn}', 'UserController@updateApi')
            ->name('api.users.update');
        Route::delete('/{fn}', 'UserController@deleteApi')
            ->name('api.users.delete');
        Route::patch('/{fn}/suspend', 'UserController@suspendApi')
            ->name('api.users.suspend');
        Route::patch('/{fn}/reactivate', 'UserController@reactivateApi')
            ->name('api.users.reactivate');
    });
    Route::prefix('/sites')->group(function () {
        Route::post('/', 'WebsiteController@storeApi')
            ->name('api.sites.add');
        Route::get('/', 'WebsiteController@dataApi')
            ->name('api.sites.show');
        Route::get('/{website}', 'WebsiteController@showApi')
            ->name('api.sites.read');
        Route::get('/list/{id}', 'WebsiteController@websiteList')
            ->name('api.sites.websites');
        Route::patch('/{website}', 'WebsiteController@updateApi')
            ->name('api.sites.update');
        Route::patch('/{website}/archive', 'WebsiteController@archiveApi')
            ->name('api.sites.archive');
        Route::patch('/{website}/unarchive', 'WebsiteController@unarchiveApi')
            ->name('api.sites.unarchive');
        Route::get('/{website}/check', 'WebsiteController@checkTracking')
            ->name('api.sites.check');
        Route::get('/{website}/force', 'WebsiteController@forceActivationApi')
            ->name('api.sites.force');
        Route::get('/{website}/js-snippet', 'WebsiteController@showJavascriptSnippet')
            ->name('api.sites.snippet.javascript');
    });
});
