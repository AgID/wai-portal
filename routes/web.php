<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * General public routes
 */

Route::get('/', [
    'as' => 'home',
    'uses' => 'HomeController@home'
]);

Route::get('/privacy', [
    'as' => 'privacy',
    'uses' => 'HomeController@privacy'
]);

Route::get('/legal-notes', [
    'as' => 'legal-notes',
    'uses' => 'HomeController@legalNotes'
]);

Route::get('/faq', [
    'as' => 'faq',
    'uses' => 'HomeController@faq'
]);

/**
 * Authentication related routes
 */

Route::prefix('/admin/user')->group(function() {
    Route::get('/login', [
        'as' => 'admin-login',
        'uses' => 'Auth\AdminAuthController@showLoginForm'
    ]);

    Route::post('/login', [
        'as' => 'admin-do_login',
        'uses' => 'Auth\AdminAuthController@login'
    ]);

    Route::get('/logout', [
        'as' => 'admin-logout',
        'uses' => 'Auth\AdminAuthController@logout'
    ]);

    Route::get('/password-forgot', [
        'as' => 'admin-password_forgot',
        'uses' => 'Auth\AdminAuthController@showPasswordForgotForm'
    ]);

    Route::post('/password-forgot', [
        'as' => 'admin-send_reset_password',
        'uses' => 'Auth\AdminAuthController@sendPasswordForgotEmail'
    ])->middleware('throttle:5,1');

    Route::get('/password-reset/{token?}', [
        'as' => 'admin-password_reset',
        'uses' => 'Auth\AdminAuthController@showPasswordResetForm'
    ]);

    Route::post('/password-reset', [
        'as' => 'admin-do_password_reset',
        'uses' => 'Auth\AdminAuthController@passwordReset'
    ])->middleware('throttle:5,1');

    Route::get('/verify', [
        'as' => 'admin-verify',
        'uses' => 'Auth\AdminVerificationController@verify'
    ]);

    Route::get('/verify/token/{email?}/{token?}', [
        'as' => 'admin-do_verify',
        'uses' => 'Auth\AdminVerificationController@verifyToken'
    ])->middleware('throttle:5,1');

    Route::get('/verify/resend', [
        'as' => 'admin-verify_resend',
        'uses' => 'Auth\AdminVerificationController@showResendForm'
    ]);

    Route::post('/verify/resend', [
        'as' => 'admin-do_verify_resend',
        'uses' => 'Auth\AdminVerificationController@resend'
    ])->middleware('throttle:5,1');
});

/** Only SPID authentication without application authentication */
Route::prefix('/register')->middleware(['spid.auth', 'guest'])->group(function() {
    Route::get('/', [
        'as' => 'auth-register',
        'uses' => 'Auth\RegisterController@index'
    ]);

    Route::post('/', [
        'as' => 'auth-do_register',
        'uses' => 'Auth\RegisterController@register'
    ]);
});

/** Only SPID authentication */
Route::prefix('/user/verify')->middleware('spid.auth')->group(function() {
    Route::get('/', [
        'as' => 'auth-verify',
        'uses' => 'Auth\VerificationController@verify'
    ]);

    Route::get('/token/{token?}', [
        'as' => 'auth-do_verify',
        'uses' => 'Auth\VerificationController@verifyToken'
    ]);

    Route::get('/resend', [
        'as' => 'auth-verify_resend',
        'uses' => 'Auth\VerificationController@resend'
    ]);
});

/**
 * Application routes
 */

/** Both SPID and application authentication: this is the default for registered users */
Route::middleware(['spid.auth', 'auth'])->group(function() {
    Route::get('/user/profile', [
        'as' => 'user_profile',
        'uses' => 'Auth\UserAuthController@profile'
    ]);

    Route::prefix('/dashboard')->group(function() {
        Route::get('/', [
            'as' => 'dashboard',
            'uses' => 'DashboardController@index'
        ]);

        Route::post('/search-ipa-list', [
            'as' => 'search-ipa-list',
            'uses' => 'SearchIPAListController@search'
        ]);

        Route::prefix('/websites')->group(function() {
            Route::get('/', [
                'as' => 'websites-index',
                'uses' => 'WebsiteController@index'
            ]);

            Route::get('/add-primary', [
                'as' => 'websites-add-primary',
                'uses' => 'WebsiteController@createPrimary'
            ]);

            Route::post('/store-primary', [
                'as' => 'websites-store-primary',
                'uses' => 'WebsiteController@storePrimary'
            ]);

            Route::get('/add', [
                'as' => 'websites-add',
                'uses' => 'WebsiteController@create'
            ])->middleware('authorize.analytics:manage-sites');

            Route::post('/store', [
                'as' => 'websites-store',
                'uses' => 'WebsiteController@store'
            ])->middleware('authorize.analytics:manage-sites');

            Route::get('/{website}/edit', [
                'as' => 'websites-edit',
                'uses' => 'WebsiteController@edit'
            ])->middleware('authorize.analytics:manage-sites');

            Route::post('/{website}/update', [
                'as' => 'websites-update',
                'uses' => 'WebsiteController@update'
            ])->middleware('authorize.analytics:manage-sites');

            Route::get('/{website}/javascript-snippet', [
                'as' => 'website-javascript-snippet',
                'uses' => 'WebsiteController@showJavascriptSnippet'
            ])->middleware('authorize.analytics:read-analytics');

            Route::get('/data', [
                'as' => 'websites-data-json',
                'uses' => 'WebsiteController@dataJson'
            ]);
        });

        Route::prefix('/users')->group(function() {
            Route::get('/', [
                'as' => 'users-index',
                'uses' => 'UserController@index'
            ])->middleware('authorize.analytics:read-analytics');

            Route::get('/data', [
                'as' => 'users-data-json',
                'uses' => 'UserController@dataJson'
            ])->middleware('authorize.analytics:read-analytics');

            Route::get('/add', [
                'as' => 'users-create',
                'uses' => 'UserController@create'
            ])->middleware('authorize.analytics:manage-users');

            Route::post('/store', [
                'as' => 'users-store',
                'uses' => 'UserController@store'
            ])->middleware('authorize.analytics:manage-users');

            Route::get('/{user}/edit', [
                'as' => 'users-edit',
                'uses' => 'UserController@edit'
            ])->middleware('authorize.analytics:manage-users');

            Route::post('/{user}/update', [
                'as' => 'users-update',
                'uses' => 'UserController@update'
            ])->middleware('authorize.analytics:manage-users');
        });
    });
    Route::prefix('/analytics-service')->group(function() {
        Route::get('/login', [
            'as' => 'analytics-service-login',
            'uses' => 'AnalyticsController@login'
        ]);
    });
});

/**
 * Admin-only application routes
 */
Route::middleware(['admin-auth'])->group(function() {
    Route::prefix('/admin')->group(function() {
        Route::get('/', function (){
            return redirect()->route('admin-dashboard');
        });

        Route::get('/dashboard', [
            'as' => 'admin-dashboard',
            'uses' => 'AdminController@dashboard'
        ]);

        Route::get('/users/add', [
            'as' => 'admin-user_add',
            'uses' => 'AdminUserController@create'
        ]);

        Route::post('/users/store', [
            'as' => 'admin-user_store',
            'uses' => 'AdminUserController@store'
        ]);

        Route::get('/users/{user}/show', [
            'as' => 'admin-user_show',
            'uses' => 'AdminUserController@show'
        ]);

        Route::get('/users/{user}/edit', [
            'as' => 'admin-user_edit',
            'uses' => 'AdminUserController@edit'
        ]);

        Route::post('/users/{user}/update', [
            'as' => 'admin-user_update',
            'uses' => 'AdminUserController@update'
        ]);

        Route::get('/user/change-password', [
            'as' => 'admin-password_change',
            'uses' => 'Auth\AdminAuthController@showPasswordChangeForm'
        ]);

        Route::post('/user/change-password', [
            'as' => 'admin-do_password_change',
            'uses' => 'Auth\AdminAuthController@passwordChange'
        ]);
    });
});
