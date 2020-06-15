<?php

namespace Tests;

use App\Enums\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;
    use SqliteForeignKeyHotfix;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->hotfixSqlite();
    }

    /**
     * Setup the browser test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('app:init-permissions');
    }

    /**
     * Inject a SPID session.
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return void
     */
    public function injectFakeSpidSession()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/_test/_inject_spid_session');
        });
    }

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function prepare()
    {
        static::useChromedriver('/usr/local/bin/chromedriver');
        static::startChromeDriver();
    }

    /**
     * Get the verification signed url for the specified user.
     *
     * @param int $userId
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return string
     */
    protected function getSignedUrl(int $userId)
    {
        $user = User::find($userId);
        $verificationRoute = $user->isA(UserRole::SUPER_ADMIN)
            ? 'admin.verification.verify'
            : 'verification.verify';

        return URL::temporarySignedRoute(
            $verificationRoute,
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'uuid' => $user->uuid,
                'hash' => base64_encode(Hash::make($user->email)),
            ]
        );
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions())->addArguments([
            '--headless',
            '--no-sandbox',
            '--disable-gpu',
            '--allow-running-insecure-content',
            '--remote-debugging-port=9222',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()
                ->setCapability(ChromeOptions::CAPABILITY, $options)
                ->setCapability('acceptInsecureCerts', true)
                ->setCapability('acceptSslCerts', true)
        );
    }
}
