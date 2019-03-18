<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use Tests\Browser\Pages\Home;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('app:create-roles');
        $this->browse(function (Browser $browser) {
            $browser->visit(new Home())->press('ACCETTO');
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
     * Add a fake registered user.
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return void
     */
    public function addFakeUser()
    {
        $this->artisan('db:seed', ['--class' => 'UsersTableSeeder']);
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
     * Assign roles to specified user.
     *
     * @param int $userId
     * @param string $role
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return void
     */
    public function assignFakeRole(int $userId, string $role)
    {
        $this->browse(function (Browser $browser) use ($userId, $role) {
            $browser->visit('/_test/_assign_role/' . $userId . '/' . $role);
        });
    }

    /**
     * Get the verification token for the specified user.
     *
     * @param int $userId
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return string
     */
    public function getVerificationToken(int $userId)
    {
        $verificationToken = '';
        $this->browse(function (Browser $browser) use ($userId, &$verificationToken) {
            $response = $browser->visit('/_test/_get_new_user_verification_token/' . $userId);
            $verificationToken = strip_tags($response->driver->getPageSource());
        });

        return $verificationToken;
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
            '--allow-running-insecure-content',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()
                ->setCapability(ChromeOptions::CAPABILITY, $options)
                ->setCapability('acceptInsecureCerts', true)
                ->setCapability('acceptSslCerts', true)
        );
    }
}
