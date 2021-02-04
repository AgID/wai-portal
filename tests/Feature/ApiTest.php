<?php

namespace Tests\Feature;

use App\Models\Credential;
use App\Models\PublicAdministration;
use Faker\Factory;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Public Administration analytics dashboard controller tests.
 */
class ApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The Kong credential.
     *
     * @var the Kong credential
     */
    private $credential;

    /**
     * The public administration.
     *
     * @var PublicAdministration the public administration
     */
    private $publicAdministration;

    /**
     * Fake data generator.
     *
     * @var Generator the generator
     */
    private $faker;

    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app.url', 'https://nginx');

        $this->client = new Client(['base_uri' => config('app.url')]);

        $this->publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();

        $this->credential = factory(Credential::class)->create([
            'public_administration_id' => $this->publicAdministration->id,
        ]);

        $this->faker = Factory::create();
    }

    /**
     * Test API request fails due to missing consumer ID header.
     */
    public function testApiErrorNoConsumerId(): void
    {
        $response = $this->client->request('GET', route('api.sites.show', [], false), [
            'headers' => [
                'X-Consumer-Custom-Id' => '"{\"type\":\"admin\",\"siteId\":[1,11,2,16,21]}"',
            ],
            'verify' => false,
            'http_errors' => false,
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testApiErrorNoCustomId(): void
    {
        $response = $this->client->request('GET', route('api.sites.show', [], false), [
            'headers' => [
                'X-Consumer-Id' => 'f8846ee4-031d-4a1b-88d5-08efc0d44eb2',
            ],
            'verify' => false,
            'http_errors' => false,
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test API request fails as Credential type is not "admin".
     */
    public function testApiErrorAnalyticsType(): void
    {
        $response = $this->client->request('GET', route('api.sites.show', [], false), [
            'headers' => [
                'X-Consumer-Custom-Id' => '"{\"type\":\"analytics\",\"siteId\":[1,11,2,16,21]}"',
                'X-Consumer-Id' => 'f8846ee4-031d-4a1b-88d5-08efc0d44eb2',
            ],
            'verify' => false,
            'http_errors' => false,
        ]);

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test API should pass.
     */
    public function testApiAdminType(): void
    {
        $response = $this->json('GET', route('api.sites.show'), [], [
            'X-Consumer-Custom-Id' => '{"type":"admin","siteId":[1,11,2,16,21]}',
            'X-Consumer-Id' => $this->credential->consumer_id,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
