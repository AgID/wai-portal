<?php

use App\Enums\WebsiteStatus;
use App\Models\Website;
use GuzzleHttp\Client as TrackingClient;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Silber\Bouncer\BouncerFacade as Bouncer;

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

Route::get('/_reset_all', function () {
    session()->invalidate();
    $session_files = Storage::disk('sessions')->files('/');
    $session_files = array_diff($session_files, ['.gitignore']);
    Storage::disk('sessions')->delete($session_files);
    Artisan::call('migrate:fresh');
    Bouncer::scope()->to(null);
    Artisan::call('app:init-permissions');
    Artisan::call('db:seed');

    return redirect()->home()->withMessage(['info' => "L'istanza di " . config('app.name') . ' è stata ripristinata allo stato iniziale.']);
});

Route::get('/_activate_websites', function () {
    $faker = Faker\Factory::create();
    $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
    $pendingWebsites = Website::where('status', WebsiteStatus::PENDING)->get();
    $pendingWebsites->map(function ($website) use ($client, $faker) {
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $website->analytics_id,
            ],
            'verify' => false,
            'headers' => [
                'User-Agent' => $faker->userAgent,
            ],
        ]);
    });
    Artisan::call('app:check-websites');

    return redirect()->home()->withMessage(['success' => 'I siti web in attesa sono stati attivati.']);
});
