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

    $analyticsService = app()->make('analytics-service');
    foreach ($analyticsService->getAllSitesId() as $siteId) {
        if ('1' !== $siteId) {
            $analyticsService->deleteSite($siteId);
        }
    }

    foreach ($analyticsService->getUsersLogin() as $userLogin) {
        if (!in_array($userLogin, ['admin', 'anonymous'])) {
            $analyticsService->deleteUser($userLogin);
        }
    }

    return redirect()->home()->withNotification([
        'title' => __('reset applicazione'),
        'message' => __("L'istanza staging di :app è stata ripristinata allo stato iniziale.", ['app' => config('app.name')]),
        'status' => 'info',
        'icon' => 'it-info-circle',
    ]);
});

Route::get('/_generate_visits', function () {
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

    return redirect()->home()->withNotification([
        'title' => __('generazione visite'),
        'message' => implode("\n", [
            __('Sono state generate delle visite di test verso i siti in stato di attesa.'),
            __("Da adesso è possibile procedere con il check dell'attivazione."),
        ]),
        'status' => 'info',
        'icon' => 'it-info-circle',
    ]);
});
