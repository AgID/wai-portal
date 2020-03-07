<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Enums\Logs\JobType;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Monolog\Logger;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Logs visualization test.
 */
class LogsVisualizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The super admin user.
     *
     * @var User the super admin user
     */
    private $superAdmin;

    /**
     * The admin user.
     *
     * @var User the user
     */
    private $user;

    /**
     * The first public administration.
     *
     * @var PublicAdministration the first public administration
     */
    private $firstPublicAdministration;

    /**
     * The second public administration.
     *
     * @var PublicAdministration the second public administration
     */
    private $secondPublicAdministration;

    /**
     * The website.
     *
     * @var Website the website
     */
    private $website;

    /**
     * Pre-test setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Bouncer::dontCache();

        $this->superAdmin = factory(User::class)->create([
            'email_verified_at' => Carbon::now(),
        ]);
        Bouncer::scope()->to(0);
        $this->superAdmin->assign(UserRole::SUPER_ADMIN);
        $this->superAdmin->allow(UserPermission::ACCESS_ADMIN_AREA);

        $this->user = factory(User::class)->create([
            'email_verified_at' => Carbon::now(),
        ]);
        $this->firstPublicAdministration = factory(PublicAdministration::class)->create();
        $this->user->publicAdministrations()->sync($this->firstPublicAdministration->id);

        Bouncer::scope()->to($this->firstPublicAdministration->id);
        $this->user->assign(UserRole::ADMIN);
        $this->user->allow(UserPermission::VIEW_LOGS);

        $this->website = factory(Website::class)->create([
            'public_administration_id' => $this->firstPublicAdministration->id,
        ]);

        $this->secondPublicAdministration = factory(PublicAdministration::class)->create();

        $client = new Client(['base_uri' => 'http://' . config('elastic-search.host') . ':' . config('elastic-search.port')]);
        try {
            $client->request('PUT', config('elastic-search.index_name'), []);
        } catch (ClientException $e) {
            if (!Str::contains($e->getMessage(), 'resource_already_exists_exception')) {
                throw $e;
            }
        }
    }

    /**
     * Post-test tear down.
     */
    protected function tearDown(): void
    {
        $client = new Client(['base_uri' => 'http://' . config('elastic-search.host') . ':' . config('elastic-search.port')]);
        $client->request('DELETE', config('elastic-search.index_name'), []);
        parent::tearDown();
    }

    /**
     * Test data validation successful.
     */
    public function testValidationSuccessful(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->json(
                'GET',
                route('admin.logs.data'),
                [
                    'draw' => 0,
                    'start' => 0,
                    'length' => 10,
                    'message' => 'testing',
                    'order' => [
                        [
                            'dir' => 'asc',
                        ],
                    ],
                    'start_date' => Carbon::now()->format('d/m/Y'),
                    'start_time' => Carbon::now()->subMinutes(15)->format('H:i'),
                    'end_date' => Carbon::now()->format('d/m/Y'),
                    'end_time' => Carbon::now()->format('H:i'),
                    'pa' => $this->firstPublicAdministration->name,
                    'website' => $this->website->name,
                    'user' => $this->user->uuid,
                    'ipa_code' => $this->firstPublicAdministration->ipa_code,
                    'website_id' => $this->website->id,
                    'user_uuid' => $this->user->uuid,
                    'event' => EventType::UPDATE_PA_FROM_IPA_COMPLETED,
                    'exception_type' => ExceptionType::TENANT_SELECTION,
                    'job' => JobType::SEND_RESET_PASSWORD_TOKEN,
                    'severity' => Logger::ERROR,
                ]
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
        $response->assertJsonFragment([
            'draw' => 0,
        ]);
    }

    /**
     * Test data validation fail.
     */
    public function testValidationFail(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->json('GET', route('admin.logs.data'), [
                'message' => 0,
                'order' => [
                    [
                        'dir' => 'failing',
                    ],
                ],
                'start_time' => Carbon::now()->format('H:i'),
                'end_time' => Carbon::now()->subMinutes(15)->format('H:i'),
                'event' => -1,
                'exception_type' => -1,
                'job' => -1,
                'severity' => -1,
            ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'draw',
            'start',
            'length',
            'message',
            'order.0.dir',
            'start_date',
            'end_date',
            'event',
            'exception_type',
            'job',
            'severity',
        ]);
    }

    /**
     * Test users search capabilities.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to push logs
     */
    public function testSearchingCapabilities(): void
    {
        $client = new Client(['base_uri' => 'http://' . config('elastic-search.host') . ':' . config('elastic-search.port')]);

        $message1 = 'WAI Testing message 1';
        $client->request('POST', config('elastic-search.index_name') . '/_doc', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => $message1,
                'channel' => config('app.env'),
                'level' => Logger::INFO,
                'level_name' => 'INFO',
                'datetime' => Carbon::now()->toIso8601String(),
                'context' => [
                    'pa' => $this->firstPublicAdministration->ipa_code,
                ],
            ],
        ]);

        $message2 = 'WAI Testing message 2';
        $client->request('POST', config('elastic-search.index_name') . '/_doc', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => $message2,
                'channel' => config('app.env'),
                'level' => Logger::DEBUG,
                'level_name' => 'DEBUG',
                'datetime' => Carbon::now()->toIso8601String(),
                'context' => [
                    'pa' => $this->firstPublicAdministration->ipa_code,
                ],
            ],
        ]);

        $message3 = 'WAI Testing message 3';
        $client->request('POST', config('elastic-search.index_name') . '/_doc', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => $message3,
                'channel' => config('app.env'),
                'level' => Logger::INFO,
                'level_name' => 'INFO',
                'datetime' => Carbon::now()->toIso8601String(),
                'context' => [
                    'pa' => $this->secondPublicAdministration->ipa_code,
                ],
            ],
        ]);

        $message4 = 'WAI Testing message 4';
        $client->request('POST', config('elastic-search.index_name') . '/_doc/?refresh=wait_for', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => $message4,
                'channel' => config('app.env'),
                'level' => Logger::ERROR,
                'level_name' => 'ERROR',
                'datetime' => Carbon::now()->toIso8601String(),
                'context' => [
                    'user' => $this->user->uuid,
                ],
            ],
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->json('GET', route('admin.logs.data'), [
                'draw' => 0,
                'start' => 0,
                'length' => 10,
                'order' => [
                    [
                        'dir' => 'asc',
                    ],
                ],
            ]);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'message' => $message1,
            'raw' => 'INFO',
        ]);

        $response->assertJsonFragment([
            'message' => $message2,
            'raw' => 'DEBUG',
        ]);

        $response->assertJsonFragment([
            'message' => $message3,
            'raw' => 'INFO',
        ]);

        $response->assertJsonFragment([
            'message' => $message4,
            'raw' => 'ERROR',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->firstPublicAdministration->id,
            ])
            ->json('GET', route('logs.data'), [
                'draw' => 0,
                'start' => 0,
                'length' => 10,
                'order' => [
                    [
                        'dir' => 'asc',
                    ],
                ],
            ]);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'message' => $message1,
            'raw' => 'INFO',
        ]);

        $response->assertDontSee(json_encode([
            'message' => $message2,
            'raw' => 'DEBUG',
        ]));

        $response->assertDontSee(json_encode([
            'message' => $message3,
            'raw' => 'INFO',
        ]));

        $response->assertDontSee(json_encode([
            'message' => $message4,
            'raw' => 'INFO',
        ]));
    }
}
