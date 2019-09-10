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
| - email verification routes;
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
        'as' => 'admin.login.show',
        'uses' => 'Auth\SuperAdminAuthController@showLogin',
    ])->middleware('guest');

    Route::post('/login', [
        'as' => 'admin.login',
        'uses' => 'Auth\SuperAdminAuthController@login',
    ])->middleware('guest');

    Route::get('/logout', [
        'as' => 'admin.logout',
        'uses' => 'Auth\SuperAdminAuthController@logout',
    ])->middleware('admin.auth');

    Route::get('/password-forgot', [
        'as' => 'admin.password.forgot.show',
        'uses' => 'Auth\SuperAdminAuthController@showPasswordForgot',
    ])->middleware('guest');

    Route::post('/password-forgot', [
        'as' => 'admin.password.reset.send',
        'uses' => 'Auth\SuperAdminAuthController@sendPasswordForgot',
    ])->middleware('guest', 'throttle:5,1');

    Route::get('/password-reset/{token?}', [
        'as' => 'admin.password.reset.show',
        'uses' => 'Auth\SuperAdminAuthController@showPasswordReset',
    ])->middleware('guest');

    Route::post('/password-reset', [
        'as' => 'admin.password.reset',
        'uses' => 'Auth\SuperAdminAuthController@passwordReset',
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

    Route::prefix('/admin/user/profile')->group(function () {
        Route::get('/', [
            'as' => 'admin.user.profile',
            'uses' => 'Auth\ProfileController@show',
        ]);

        Route::get('/edit', [
            'as' => 'admin.user.profile.edit',
            'uses' => 'Auth\ProfileController@edit',
        ]);

        Route::patch('/', [
            'as' => 'admin.user.profile.update',
            'uses' => 'Auth\ProfileController@update',
        ]);
    });
});

/*
 * Registration routes.
 *
 * Only SPID authentication required.
 */
Route::middleware('spid.auth', 'guest')->group(function () {
    Route::prefix('/register')->group(function () {
        Route::get('/', [
            'as' => 'auth.register.show',
            'uses' => 'Auth\RegisterController@showRegistrationForm',
        ]);

        Route::post('/', [
            'as' => 'auth.register',
            'uses' => 'Auth\RegisterController@register',
        ]);
    });
});

/*
 * Email verification routes.
 *
 * SPID authentication required.
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
});

/*
 * User profile routes.
 *
 * Both SPID and application authentication required
 */
Route::middleware('spid.auth', 'auth')->group(function () {
    Route::prefix('/user/profile')->group(function () {
        Route::get('/', [
            'as' => 'user.profile',
            'uses' => 'Auth\ProfileController@show',
        ]);

        Route::get('/edit', [
            'as' => 'user.profile.edit',
            'uses' => 'Auth\ProfileController@edit',
        ]);

        Route::patch('/', [
            'as' => 'user.profile.update',
            'uses' => 'Auth\ProfileController@update',
        ]);
    });
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
                    'as' => 'websites.index',
                    'uses' => 'WebsiteController@index',
                ]);

                Route::get('/add-primary', [
                    'as' => 'websites.create.primary',
                    'uses' => 'WebsiteController@createPrimary',
                ]);

                Route::post('/store-primary', [
                    'as' => 'websites.store.primary',
                    'uses' => 'WebsiteController@storePrimary',
                ]);

                Route::middleware('authorize.analytics:' . UserPermission::MANAGE_WEBSITES)->group(function () {
                    Route::get('/add', [
                        'as' => 'websites.create',
                        'uses' => 'WebsiteController@create',
                    ]);

                    Route::get('/users-data/{website?}', [
                        'as' => 'websites.users.permissions.data',
                        'uses' => 'WebsiteController@dataUsersPermissionsJson',
                    ]);

                    Route::post('/store', [
                        'as' => 'websites.store',
                        'uses' => 'WebsiteController@store',
                    ]);

                    Route::get('/{website}/show', [
                        'as' => 'websites.show',
                        'uses' => 'WebsiteController@show',
                    ]);

                    Route::patch('/{website}/archive', [
                        'as' => 'website.archive',
                        'uses' => 'WebsiteController@archive',
                    ]);

                    Route::patch('/{website}/unarchive', [
                        'as' => 'website.unarchive',
                        'uses' => 'WebsiteController@unarchive',
                    ]);

                    Route::get('/{website}/edit', [
                        'as' => 'websites.edit',
                        'uses' => 'WebsiteController@edit',
                    ]);

                    Route::put('/{website}', [
                        'as' => 'websites.update',
                        'uses' => 'WebsiteController@update',
                    ]);
                });

                Route::middleware('authorize.analytics:' . UserPermission::READ_ANALYTICS)->group(function () {
                    Route::get('/{website}/check', [
                        'as' => 'websites.tracking.check',
                        'uses' => 'WebsiteController@checkTracking',
                        // Authorization for specific websites is handled in the middleware
                    ]);

                    Route::get('/{website}/javascript-snippet', [
                        'as' => 'websites.snippet.javascript',
                        'uses' => 'WebsiteController@showJavascriptSnippet',
                        // Authorization for specific websites is handled in the middleware
                    ]);
                });

                Route::get('/data', [
                    'as' => 'websites.data.json',
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
                        'as' => 'users.websites.permissions.data.json',
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
                'uses' => 'AdminDashboardController@dashboard',
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
                    'uses' => 'SuperAdminController@index',
                ]);

                Route::get('/data', [
                    'as' => 'admin.users.data.json',
                    'uses' => 'SuperAdminController@dataJson',
                ]);

                Route::get('/add', [
                    'as' => 'admin.users.create',
                    'uses' => 'SuperAdminController@create',
                ]);

                Route::post('/', [
                    'as' => 'admin.users.store',
                    'uses' => 'SuperAdminController@store',
                ]);

                Route::get('/{user}/show', [
                    'as' => 'admin.users.show',
                    'uses' => 'SuperAdminController@show',
                ]);

                Route::get('/{user}/edit', [
                    'as' => 'admin.users.edit',
                    'uses' => 'SuperAdminController@edit',
                ]);

                Route::patch('/{user}/update', [
                    'as' => 'admin.users.update',
                    'uses' => 'SuperAdminController@update',
                ]);

                Route::patch('/{user}/suspend', [
                    'as' => 'admin.users.suspend',
                    'uses' => 'SuperAdminController@suspend',
                ]);

                Route::patch('/{user}/reactivate', [
                    'as' => 'admin.users.reactivate',
                    'uses' => 'SuperAdminController@reactivate',
                ]);
            });

            Route::prefix('/{publicAdministration}')->group(function () {
                Route::get('/', [
                    'as' => 'admin.publicAdministration.index',
                    'uses' => 'DashboardController@index',
                ]);

                Route::prefix('/users')->group(function () {
                    Route::get('/', [
                        'as' => 'admin.publicAdministration.users.index',
                        'uses' => 'UserController@index',
                    ]);

                    Route::get('/data', [
                        'as' => 'admin.publicAdministration.users.data.json',
                        'uses' => 'UserController@dataJson',
                    ]);

                    Route::get('/add', [
                        'as' => 'admin.publicAdministration.users.create',
                        'uses' => 'UserController@create',
                    ]);

                    Route::get('/websites-data/{user?}', [
                        'as' => 'admin.publicAdministration.users.websites.permissions.data.json',
                        'uses' => 'UserController@dataWebsitesPermissionsJson',
                    ]);

                    Route::post('/', [
                        'as' => 'admin.publicAdministration.users.store',
                        'uses' => 'UserController@store',
                    ]);

                    Route::get('/{user}/show', [
                        'as' => 'admin.publicAdministration.users.show',
                        'uses' => 'UserController@show',
                    ]);

                    Route::get('/{user}/edit', [
                        'as' => 'admin.publicAdministration.users.edit',
                        'uses' => 'UserController@edit',
                    ]);

                    Route::patch('/{user}/update', [
                        'as' => 'admin.publicAdministration.users.update',
                        'uses' => 'UserController@update',
                    ]);

                    Route::patch('/{user}/suspend', [
                        'as' => 'admin.publicAdministration.users.suspend',
                        'uses' => 'UserController@suspend',
                    ]);

                    Route::patch('/{user}/reactivate', [
                        'as' => 'admin.publicAdministration.users.reactivate',
                        'uses' => 'UserController@reactivate',
                    ]);

                    Route::patch('/{trashed_user}/restore', [
                        'as' => 'admin.publicAdministration.users.restore',
                        'uses' => 'UserController@restore',
                    ]);

                    Route::patch('/{user}/delete', [
                        'as' => 'admin.publicAdministration.users.delete',
                        'uses' => 'UserController@delete',
                    ]);
                });

                Route::prefix('/websites')->group(function () {
                    Route::get('/', [
                        'as' => 'admin.publicAdministration.websites.index',
                        'uses' => 'WebsiteController@index',
                    ]);

                    Route::get('/data', [
                        'as' => 'admin.publicAdministration.websites.data.json',
                        'uses' => 'WebsiteController@dataJson',
                    ]);

                    Route::patch('/{trashed_website}/restore', [
                        'as' => 'admin.publicAdministration.websites.restore',
                        'uses' => 'WebsiteController@restore',
                    ]);

                    Route::patch('/{website}/delete', [
                        'as' => 'admin.publicAdministration.websites.delete',
                        'uses' => 'WebsiteController@delete',
                    ]);
                });
            });
        });

        Route::get('/user/change-password', [
            'as' => 'admin.password.change.show',
            'uses' => 'Auth\SuperAdminAuthController@showPasswordChange',
        ]);

        Route::post('/user/change-password', [
            'as' => 'admin.password.change',
            'uses' => 'Auth\SuperAdminAuthController@passwordChange',
        ]);
    });
});

Route::fallback(function () {
    return response()->view('errors.404');
});
