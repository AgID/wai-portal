<?php

namespace Tests;

use Tests\Browser\Pages\Home;

use Laravel\Dusk\TestCase as BaseTestCase;
use Laravel\Dusk\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        static::useChromedriver('/usr/local/bin/chromedriver');
        static::startChromeDriver();
        touch(__DIR__.'/../storage/logs/testing.log');
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--headless',
            '--no-sandbox',
            '--allow-running-insecure-content'
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()
                ->setCapability(ChromeOptions::CAPABILITY, $options)
                ->setCapability('acceptInsecureCerts', true)
                ->setCapability('acceptSslCerts', true)
        );
    }

    protected function setUp()
    {
        parent::setUp();
//        $this->artisan('migrate:refresh');
        $this->artisan('app:create-roles');
        $this->browse(function (Browser $browser) {
            $browser->visit(new Home)->press('ACCETTO');
        });
    }

    /**
     * Add a fake registered user
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function addFakeUser() {
        $this->artisan('db:seed', ['--class' => 'UsersTableSeeder']);
    }

    /**
     * Inject a SPID session
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function injectFakeSpidSession() {
        $this->browse(function (Browser $browser) {
            $browser->visit('/_test/_inject_spid_session');
        });
    }

    /**
     * Assign roles to specified user
     *
     * @param int $userId
     * @param string $role
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function assignFakeRole(int $userId, string $role) {
        $this->browse(function (Browser $browser) use ($userId, $role) {
            $browser->visit('/_test/_assign_role/'.$userId.'/'.$role);
        });
    }

    /**
     * Get the verification token for the specified user
     *
     * @param int $userId
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function getVerificationToken(int $userId) {
        $verificationToken = '';
        $this->browse(function (Browser $browser) use ($userId, &$verificationToken) {
            $response = $browser->visit('/_test/_get_user_verification_token/'.$userId);
            $verificationToken = strip_tags($response->driver->getPageSource());
        });
        return $verificationToken;
    }
}
