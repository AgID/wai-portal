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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Monolog\Logger;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class LogsVisualizationTest extends TestCase
{
    use RefreshDatabase;

    private $superAdmin;

    private $firstUser;

    private $firstPublicAdministration;

    private $secondPublicAdministration;

    private $firstWebsite;

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

        $this->firstUser = factory(User::class)->create([
            'email_verified_at' => Carbon::now(),
        ]);
        $this->firstPublicAdministration = factory(PublicAdministration::class)->create();
        $this->firstUser->publicAdministrations()->sync($this->firstPublicAdministration->id);

        Bouncer::scope()->to($this->firstPublicAdministration->id);
        $this->firstUser->assign(UserRole::ADMIN);
        $this->firstUser->allow(UserPermission::VIEW_LOGS);

        $this->firstWebsite = factory(Website::class)->create([
            'public_administration_id' => $this->firstPublicAdministration->id,
        ]);

        $this->secondPublicAdministration = factory(PublicAdministration::class)->create();
    }

    protected function tearDown(): void
    {
        $client = new Client(['base_uri' => 'http://' . config('elastic-search.host') . ':' . config('elastic-search.port')]);
        $client->request(
            'POST',
            config('elastic-search.index_name') . '/_delete_by_query',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'query' => [
                        'match' => [
                            'channel' => config('app.env'),
                        ],
                    ],
                ],
            ]
        );
        parent::tearDown();
    }

    public function testValidationSuccessful(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(
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
                    'date' => Carbon::now()->format('d/m/Y'),
                    'start_time' => Carbon::now()->subMinutes(15)->format('H:i'),
                    'end_time' => Carbon::now()->format('H:i'),
                    'ipa_code' => $this->firstPublicAdministration->ipa_code,
                    'slug' => $this->website->slug,
                    'uuid' => $this->user->uuid,
                    'event' => EventType::IPA_UPDATE_COMPLETED,
                    'exception' => ExceptionType::TENANT_SELECTION,
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

    public function testValidationFail(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(
                route('admin.logs.data'),
                [
                    'message' => 0,
                    'order' => [
                        [
                            'dir' => 'failing',
                        ],
                    ],
                    'start_time' => Carbon::now()->format('H:i'),
                    'end_time' => Carbon::now()->subMinutes(15)->format('H:i'),
                    'pa' => $this->firstPublicAdministration->name,
                    'website' => $this->firstWebsite->name,
                    'user' => $this->firstUser->name,
                    'event' => -1,
                    'exception' => -1,
                    'job' => -1,
                    'severity' => -1,
                ],
                );

        $response->assertStatus(302);

        $response->assertSessionHasErrors([
            'draw',
            'start',
            'length',
            'message',
            'order.0.dir',
            'date',
            'start_time',
            'end_time',
            'ipa_code',
            'slug',
            'uuid',
            'event',
            'exception',
            'job',
            'severity',
        ]);
    }

    public function testSuperAdminSearching(): void
    {
        $client = new Client(['base_uri' => 'http://' . config('elastic-search.host') . ':' . config('elastic-search.port')]);

        $message1 = 'WAI Testing message 1';
        $client->request(
            'POST',
            config('elastic-search.index_name') . '/_doc',
            [
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
            ]
        );

        $message2 = 'WAI Testing message 2';
        $client->request(
            'POST',
            config('elastic-search.index_name') . '/_doc',
            [
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
            ]
        );

        $message3 = 'WAI Testing message 3';
        $client->request(
            'POST',
            config('elastic-search.index_name') . '/_doc',
            [
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
            ]
        );

        $message4 = 'WAI Testing message 4';
        $client->request(
            'POST',
            config('elastic-search.index_name') . '/_doc/?refresh=wait_for',
            [
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
                        'pa' => $this->firstUser->uuid,
                    ],
                ],
            ]
        );

        $response = $this->actingAs($this->superAdmin)
            ->post(
                route('admin.logs.data'),
                [
                    'draw' => 0,
                    'start' => 0,
                    'length' => 10,
                    'order' => [
                        [
                            'dir' => 'asc',
                        ],
                    ],
                ]
            );

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'message' => $message1,
            'level_name' => 'INFO',
        ]);

        $response->assertJsonFragment([
            'message' => $message2,
            'level_name' => 'DEBUG',
        ]);

        $response->assertJsonFragment([
            'message' => $message3,
            'level_name' => 'INFO',
        ]);

        $response->assertJsonFragment([
            'message' => $message4,
            'level_name' => 'ERROR',
        ]);

        $response = $this->actingAs($this->firstUser)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->firstPublicAdministration->id,
            ])
            ->post(
                route('logs.data'),
                [
                    'draw' => 0,
                    'start' => 0,
                    'length' => 10,
                    'order' => [
                        [
                            'dir' => 'asc',
                        ],
                    ],
                ]
            );

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'message' => $message1,
            'level_name' => 'INFO',
        ]);

        $response->assertDontSee(json_encode([
            'message' => $message2,
            'level_name' => 'DEBUG',
        ]));

        $response->assertDontSee(json_encode([
            'message' => $message3,
            'level_name' => 'INFO',
        ]));

        $response->assertDontSee(json_encode([
            'message' => $message4,
            'level_name' => 'INFO',
        ]));
    }
}
