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
        $this->artisan('app:init-permissions');
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

    public function addFakePublicAdministration()
    {
        $responseJson = $this->get('/_test/_create_pa')->json();

        return $responseJson['id'];
    }

    public function addFakeWebsite(int $publicAdministrationId)
    {
        return $this->get('/_test/_create_website/' . $publicAdministrationId)->json();
    }

    public function createAnalyticsUser($userId)
    {
        $this->get('/_test/_create_analytics_user/' . $userId);
    }

    public function createAnalyticsSite($websiteId)
    {
        $this->get('/_test/_create_analytics_site/' . $websiteId);
    }

    public function grantAnalyticsAccessToWebsite($websiteId, $userId, $access)
    {
        $this->get('/_test/_grant_analytics_access_to_site/' . $websiteId . '/' . $userId . '/' . $access);
    }

    public function deleteAnalyticsUser($userId)
    {
        $this->get('/_test/_delete_analytics_user/' . $userId);
    }

    public function deleteAnalyticsSite($websiteId)
    {
        $this->get('/_test/_delete_analytics_site/' . $websiteId);
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

    public function injectFakeTenantId(int $publicAdminiatraionId)
    {
        $this->browse(function (Browser $browser) use ($publicAdminiatraionId) {
            $browser->visit('/_test/_inject_tenant_id/' . $publicAdminiatraionId);
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

    public function assignUserToPA(int $userId, int $paId)
    {
        $this->get('/_test/_assign_to_pa/' . $paId . '/' . $userId);
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
