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
            $browser->visit(new Home())->press('ACCETTO'); // Cookie bar
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
        $this->get('/_test/_assign_role/' . $userId . '/' . $role);
    }

    /**
     * Set the password for the specified user.
     *
     * @param int $userId
     * @param string $password
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return void
     */
    public function setPassword(int $userId, string $password)
    {
        $this->get('/_test/_set_password/' . $userId . '/' . $password);
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
    public function getSignedUrl(int $userId)
    {
        $responseJson = $this->get('/_test/_get_user_verification_signed_url/' . $userId)->json();

        return $responseJson['signed_url'];
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
