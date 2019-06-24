<?php

use App\Enums\UserPermission;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Defined routes:
| - general public routes;
| - authentication routes;
| - registration routes;
| - email varification routes;
| - application routes;
| - admin-only application routes.
|
*/

/*
 * General public routes.
 */

Route::get('/', [
    'as' => 'home',
    'uses' => 'HomeController@home',
]);

Route::get('/privacy', [
    'as' => 'privacy',
    'uses' => 'HomeController@privacy',
]);

Route::get('/legal-notes', [
    'as' => 'legal-notes',
    'uses' => 'HomeController@legalNotes',
]);

Route::get('/faq', [
    'as' => 'faq',
    'uses' => 'HomeController@faq',
]);

/*
 * Admin authentication routes.
 */
Route::prefix('/admin/user')->group(function () {
    Route::get('/login', [
        'as' => 'admin-login',
        'uses' => 'Auth\AdminAuthController@showLoginForm',
    ])->middleware('guest');

    Route::post('/login', [
        'as' => 'admin-do_login',
        'uses' => 'Auth\AdminAuthController@login',
    ])->middleware('guest');

    Route::get('/logout', [
        'as' => 'admin-logout',
        'uses' => 'Auth\AdminAuthController@logout',
    ])->middleware('admin.auth');

    Route::get('/password-forgot', [
        'as' => 'admin-password_forgot',
        'uses' => 'Auth\AdminAuthController@showPasswordForgotForm',
    ])->middleware('guest');

    Route::post('/password-forgot', [
        'as' => 'admin-send_reset_password',
        'uses' => 'Auth\AdminAuthController@sendPasswordForgotEmail',
    ])->middleware('guest', 'throttle:5,1');

    Route::get('/password-reset/{token?}', [
        'as' => 'admin-password_reset',
        'uses' => 'Auth\AdminAuthController@showPasswordResetForm',
    ])->middleware('guest');

    Route::post('/password-reset', [
        'as' => 'admin-do_password_reset',
        'uses' => 'Auth\AdminAuthController@passwordReset',
    ])->middleware('guest', 'throttle:5,1');
});

/*
 * Admin email verification routes.
 *
 * Both SPID and application authentication required.
 */
Route::middleware('admin.auth')->group(function () {
    Route::prefix('/admin/user/verify')->group(function () {
        Route::get('/', [
            'as' => 'admin.verification.notice',
            'uses' => 'Auth\VerificationController@show',
        ]);

        Route::get('/resend', [
            'as' => 'admin.verification.resend',
            'uses' => 'Auth\VerificationController@resend',
        ])->middleware('throttle:5,1');

        Route::get('/{uuid}/{hash}', [
            'as' => 'admin.verification.verify',
            'uses' => 'Auth\VerificationController@verify',
        ])->middleware('signed', 'throttle:5,1');
    });

    Route::get('/admin/user/profile', [
        'as' => 'admin.profile',
        'uses' => 'Auth\ProfileController@showProfile',
    ]);

    Route::get('/admin/user/profile/edit', [
        'as' => 'admin.profile.edit',
        'uses' => 'Auth\ProfileController@showProfileForm',
    ]);

    Route::patch('/admin/user/profile', [
        'as' => 'admin.profile.update',
        'uses' => 'Auth\ProfileController@update',
    ]);
});

/*
 * Registration routes.
 *
 * Only SPID authentication required.
 */
Route::middleware('spid.auth', 'guest')->group(function () {
    Route::prefix('/register')->group(function () {
        Route::get('/', [
            'as' => 'auth-register',
            'uses' => 'Auth\RegisterController@showRegistrationForm',
        ]);

        Route::post('/', [
            'as' => 'auth-do_register',
            'uses' => 'Auth\RegisterController@register',
        ]);
    });
});

/*
 * Email verification routes.
 *
 * Both SPID and application authentication required.
 */
Route::middleware('spid.auth')->group(function () {
    Route::prefix('/user/verify')->group(function () {
        Route::get('/', [
            'as' => 'verification.notice',
            'uses' => 'Auth\VerificationController@show',
        ]);

        Route::get('/resend', [
            'as' => 'verification.resend',
            'uses' => 'Auth\VerificationController@resend',
        ])->middleware('throttle:5,1');

        Route::get('/{uuid}/{hash}', [
            'as' => 'verification.verify',
            'uses' => 'Auth\VerificationController@verify',
        ])->middleware('signed', 'throttle:5,1');
    });

    Route::get('/user/profile', [
        'as' => 'user.profile',
        'uses' => 'Auth\ProfileController@showProfile',
    ]);

    Route::get('/user/profile/edit', [
        'as' => 'user.profile.edit',
        'uses' => 'Auth\ProfileController@showProfileForm',
    ]);

    Route::patch('/user/profile', [
        'as' => 'user.profile.update',
        'uses' => 'Auth\ProfileController@update',
    ]);
});

/*
 * Application routes.
 *
 * Both SPID and application authentication for verified users required.
 * This is the default for registered users.
 */
Route::middleware('spid.auth', 'auth', 'verified')->group(function () {
    // Route::get('/select-public-administration', [
    //     'as' => 'select-public-administration',
    //     'uses' => 'PublicAdministrationController@selectTenant',
    // ]);

    Route::middleware('tenant.selected')->group(function () {
        Route::prefix('/dashboard')->group(function () {
            Route::get('/', [
                'as' => 'dashboard',
                'uses' => 'DashboardController@index',
            ]);

            Route::get('/search-ipa-list', [
                'as' => 'search-ipa-list',
                'uses' => 'SearchIPAListController@search',
            ]);

            Route::middleware('authorize.analytics:' . UserPermission::VIEW_LOGS)->group(function () {
                Route::prefix('/logs')->group(function () {
                    Route::get('/', [
                        'as' => 'logs.show',
                        'uses' => 'Logs\LogController@show',
                    ]);
                    Route::get('/data', [
                        'as' => 'logs.data',
                        'uses' => 'Logs\LogController@data',
                    ]);
                    Route::get('/search-website-list', [
                        'as' => 'logs.search-website',
                        'uses' => 'Logs\SearchWebsiteListController@search',
                    ]);

                    Route::get('/search-user-list', [
                        'as' => 'logs.search-user',
                        'uses' => 'Logs\SearchUserListController@search',
                    ]);
                });
            });

            Route::prefix('/websites')->group(function () {
                Route::get('/', [
                    'as' => 'websites-index',
                    'uses' => 'WebsiteController@index',
                ]);

                Route::get('/add-primary', [
                    'as' => 'websites-add-primary',
                    'uses' => 'WebsiteController@createPrimary',
                ]);

                Route::post('/store-primary', [
                    'as' => 'websites-store-primary',
                    'uses' => 'WebsiteController@storePrimary',
                ]);

                Route::get('/add', [
                    'as' => 'websites-add',
                    'uses' => 'WebsiteController@create',
                ])->middleware('authorize.analytics:' . UserPermission::MANAGE_WEBSITES);

                Route::get('/webistes-data', [
                    'as' => 'websites.users.permissions.data',
                    'uses' => 'WebsiteController@dataUsersPermissionsJson',
                ])->middleware('authorize.analytics:' . UserPermission::MANAGE_WEBSITES);

                Route::post('/store', [
                    'as' => 'websites-store',
                    'uses' => 'WebsiteController@store',
                ])->middleware('authorize.analytics:' . UserPermission::MANAGE_WEBSITES);

                Route::get('/{website}/check', [
                    'as' => 'website-check_tracking',
                    'uses' => 'WebsiteController@checkTracking',
                    // Authorization for specific websites is handled in the middleware
                ])->middleware('authorize.analytics:' . UserPermission::READ_ANALYTICS);

                Route::patch('/{website}/archive', [
                    'as' => 'website.archive',
                    'uses' => 'WebsiteController@archive',
                ])->middleware('authorize.analytics:' . UserPermission::MANAGE_WEBSITES);

                Route::patch('/{website}/unarchive', [
                    'as' => 'website.unarchive',
                    'uses' => 'WebsiteController@unarchive',
                ])->middleware('authorize.analytics:' . UserPermission::MANAGE_WEBSITES);

                Route::get('/{website}/edit', [
                    'as' => 'websites-edit',
                    'uses' => 'WebsiteController@edit',
                ])->middleware('authorize.analytics:' . UserPermission::MANAGE_WEBSITES);

                Route::post('/{website}/update', [
                    'as' => 'websites-update',
                    'uses' => 'WebsiteController@update',
                ])->middleware('authorize.analytics:' . UserPermission::MANAGE_WEBSITES);

                Route::get('/{website}/javascript-snippet', [
                    'as' => 'website-javascript-snippet',
                    'uses' => 'WebsiteController@showJavascriptSnippet',
                    // Authorization for specific websites is handled in the middleware
                ])->middleware('authorize.analytics:' . UserPermission::READ_ANALYTICS);

                Route::get('/data', [
                    'as' => 'websites-data-json',
                    'uses' => 'WebsiteController@dataJson',
                ]);
            });

            Route::prefix('/users')->group(function () {
                Route::get('/', [
                    'as' => 'users.index',
                    'uses' => 'UserController@index',
                ]);

                Route::get('/data', [
                    'as' => 'users.data.json',
                    'uses' => 'UserController@dataJson',
                ]);

                Route::middleware('authorize.analytics:' . UserPermission::MANAGE_USERS)->group(function () {
                    Route::get('/add', [
                        'as' => 'users.create',
                        'uses' => 'UserController@create',
                    ]);

                    Route::get('/websites-data/{user?}', [
                        'as' => 'users.websites.permissions.data',
                        'uses' => 'UserController@dataWebsitesPermissionsJson',
                    ]);

                    Route::post('/', [
                        'as' => 'users.store',
                        'uses' => 'UserController@store',
                    ]);

                    Route::get('/{user}/show', [
                        'as' => 'users.show',
                        'uses' => 'UserController@show',
                    ]);

                    Route::get('/{user}/edit', [
                        'as' => 'users.edit',
                        'uses' => 'UserController@edit',
                    ]);

                    Route::patch('/{user}/update', [
                        'as' => 'users.update',
                        'uses' => 'UserController@update',
                    ]);

                    Route::patch('/{user}/suspend', [
                        'as' => 'users.suspend',
                        'uses' => 'UserController@suspend',
                    ]);

                    Route::patch('/{user}/reactivate', [
                        'as' => 'users.reactivate',
                        'uses' => 'UserController@reactivate',
                    ]);
                });
            });
        });

        Route::prefix('/analytics-service')->group(function () {
            Route::get('/login', [
                'as' => 'analytics-service-login',
                'uses' => 'AnalyticsController@login',
            ]);
        });
    });
});

/*
 * Admin-only application routes.
 *
 * Admin authentication required.
 */
Route::middleware('admin.auth', 'verified:admin.verification.notice')->group(function () {
    Route::prefix('/admin')->group(function () {
        Route::middleware('password.not.expired')->group(function () {
            Route::get('/', function () {
                return redirect()->route('admin.dashboard');
            });

            Route::get('/dashboard', [
                'as' => 'admin.dashboard',
                'uses' => 'AdminController@dashboard',
            ]);

            Route::prefix('/logs')->group(function () {
                Route::get('/', [
                    'as' => 'admin.logs.show',
                    'uses' => 'Logs\LogController@show',
                ]);
                Route::get('/data', [
                    'as' => 'admin.logs.data',
                    'uses' => 'Logs\LogController@data',
                ]);
                Route::get('/search-ipa-list', [
                    'as' => 'admin.logs.search-ipa-list',
                    'uses' => 'SearchIPAListController@search',
                ]);
                Route::get('/search-website-list', [
                    'as' => 'admin.logs.search-website',
                    'uses' => 'Logs\SearchWebsiteListController@search',
                ]);
                Route::get('/search-user-list', [
                    'as' => 'admin.logs.search-user',
                    'uses' => 'Logs\SearchUserListController@search',
                ]);
            });

            Route::prefix('/users')->group(function () {
                Route::get('/', [
                    'as' => 'admin.users.index',
                    'uses' => 'AdminUserController@index',
                ]);

                Route::get('/data', [
                    'as' => 'admin.users.data.json',
                    'uses' => 'AdminUserController@dataJson',
                ]);
                Route::get('/add', [
                    'as' => 'admin.users.create',
                    'uses' => 'AdminUserController@create',
                ]);

                Route::post('/', [
                    'as' => 'admin.users.store',
                    'uses' => 'AdminUserController@store',
                ]);
            });
        });

        Route::get('/user/change-password', [
            'as' => 'admin-password_change',
            'uses' => 'Auth\AdminAuthController@showPasswordChangeForm',
        ]);

        Route::post('/user/change-password', [
            'as' => 'admin-do_password_change',
            'uses' => 'Auth\AdminAuthController@passwordChange',
        ]);
    });
});
