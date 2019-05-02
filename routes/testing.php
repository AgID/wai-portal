<?php

use App\Enums\UserRole;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Italia\SPIDAuth\SPIDUser;

/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
|
| These route are valid only for testing and are loaded only in "testing"
| environment.
|
|                           **SECURITY ALERT**
| Never use these routes in production as they bypass every security check.
|
*/

Route::get('/_test/_create_pa', function () {
    $publicAdminiatration = factory(PublicAdministration::class)->create();

    return $publicAdminiatration->toJson();
});

Route::get('/_test/_create_website/{paId}', function ($paId) {
    $website = factory(Website::class)->create([
        'public_administration_id' => $paId,
    ]);

    return $website->toJson();
});

Route::get('/_test/_create_analytics_user/{userId}', function ($userId) {
    $user = User::find($userId);
    $user->registerAnalyticsServiceAccount();
});

Route::get('/_test/_create_analytics_site/{websiteId}', function ($websiteId) {
    $website = Website::find($websiteId);
    $analyticsId = app()->make('analytics-service')->registerSite(
        $website->name,
        $website->url,
        $website->publicAdministration->name
    );
    $website->analytics_id = $analyticsId;
    $website->save();
});

Route::get('/_test/_grant_analytics_access_to_site/{websiteId}/{userId}/{access}', function ($websiteId, $userId, $access) {
    $user = User::find($userId);
    $website = Website::find($websiteId);
    app()->make('analytics-service')->setWebsiteAccess($user->uuid, $access, $website->analytics_id, config('analytics-service.admin_token'));
});

Route::get('/_test/_delete_analytics_user/{userId}', function ($userId) {
    $user = User::find($userId);
    $user->deleteAnalyticsServiceAccount();
});

Route::get('/_test/_delete_analytics_site/{websiteId}', function ($websiteId) {
    $website = Website::find($websiteId);
    app()->make('analytics-service')->deleteSite($website->analytics_id, config('analytics-service.admin_token'));
});

Route::get('/_test/_session_put/{key}/{value}', function ($key, $value) {
    $value = json_decode($value) ?: $value;
    session([$key => $value]);
});

Route::get('/_test/_session_get/{key}', function ($key) {
    return session($key);
});

Route::get('/_test/_inject_spid_session', function () {
    $SPIDUser = new SPIDUser([
        'mobilePhone' => ['+390000000000'],
        'familyName' => ['Cognome'],
        'name' => ['Nome'],
        'spidCode' => ['TEST1234567890'],
        'fiscalNumber' => ['FSCLNB17A01H501X'],
        'email' => ['mail@example.com'],
    ]);
    session(['spid_sessionIndex' => 'fake-session-index']);
    session(['spid_user' => $SPIDUser]);
});

Route::get('/_test/_inject_tenant_id/{paId}', function ($paId) {
    session(['tenant_id' => $paId]);
});

Route::get('/_test/_assign_role/{userId}/{role}', function ($userId, $role) {
    User::find($userId)->assign($role);
});

Route::get('/_test/_assign_to_pa/{paId}/{userId}', function ($paId, $userId) {
    PublicAdministration::find($paId)->users()->sync($userId);
});

Route::get('/_test/_set_password/{userId}/{password}', function ($userId, $password) {
    $user = User::find($userId);
    $user->password = Hash::make($password);
    $user->save();
});

Route::get('/_test/_get_user_verification_signed_url/{userId}', function ($userId) {
    $user = User::find($userId);
    $verificationRoute = $user->isA(UserRole::SUPER_ADMIN)
        ? 'admin.verification.verify'
        : 'verification.verify';
    $signedUrl = URL::temporarySignedRoute(
        $verificationRoute,
        Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
        ['uuid' => $user->uuid]
    );

    return response()->json(['signed_url' => $signedUrl]);
});
