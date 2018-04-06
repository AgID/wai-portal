<?php

use App\Models\Website;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Italia\SPIDAuth\SPIDUser;
use GuzzleHttp\Client as TrackingClient;

/*
|--------------------------------------------------------------------------
| Staging Routes
|--------------------------------------------------------------------------
|
| These route are valid only for staging and are loaded only in "staging"
| environment.
|
|                           **SECURITY ALERT**
| Never use these routes in production as they bypass every security check.
|
*/

Route::get('/_fake_spid_login', function () {
    $SPIDUser = new SPIDUser([
        "mobilePhone" => ["+390000000000"],
        "familyName" => ["Rossi"],
        "name" => ["Mario"],
        "spidCode" => ["TEST1234567890"],
        "fiscalNumber" => ["FSCLNB17A01H501X"],
        "email" => ["mail@example.com"]
    ]);
    session(['spid_sessionIndex' => 'fake-session-index']);
    session(['spid_user' => $SPIDUser]);
    return redirect()->home();
});

Route::get('/_fake_spid_logout', function () {
    session()->invalidate();
    return redirect()->home();
});

Route::get('/_reset_all', function () {
    session()->invalidate();
    $session_files = Storage::disk('sessions')->files('/');
    $session_files = array_diff($session_files, ['.gitignore']);
    Storage::disk('sessions')->delete($session_files);
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');
    Artisan::call('app:create-roles');
    return redirect()->home()->withMessage(['info' => "L'istanza di " . config('app.name') . " è stata ripristinata allo stato iniziale."]);
});

Route::get('/_activate_websites', function() {
    $faker = Faker\Factory::create();
    $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
    $pendingWebsites = Website::where('status', 'pending')->get();
    $pendingWebsites->map(function ($website) use ($client, $faker) {
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $website->analytics_id
            ],
            'verify' => false,
            'headers' => [
                'User-Agent' => $faker->userAgent,
            ]
        ]);
    });
    Artisan::call('app:check-websites');
    return redirect()->home()->withMessage(['success' => "I siti web in attesa sono stati attivati."]);
});
