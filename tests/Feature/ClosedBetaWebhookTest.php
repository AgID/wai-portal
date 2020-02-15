<?php

namespace Tests\Feature;

use App\Exceptions\Handler;
use App\Jobs\UpdateClosedBetaWhitelist;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Spatie\WebhookClient\Exceptions\WebhookFailed;
use Tests\TestCase;

/**
 * Test closed beta whitelist update web hook controller.
 */
class ClosedBetaWebhookTest extends TestCase
{
    /**
     * The content YAML string.
     *
     * @var string the string
     */
    private $content;

    /**
     * The expected content signature.
     *
     * @var string the signature
     */
    private $signature;

    /**
     * Pre-tests set up.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $secret = config('webhook-client.configs.0.signing_secret');
        $this->content = [
            'ref' => 'fake',
            'repository' => [
                'full_name' => 'owner/repo',
            ],
        ];
        $this->signature = 'sha1=' . hash_hmac('sha1', json_encode($this->content), $secret);

        Config::set('wai.closed_beta', false);
        Config::set('app.url', 'https://nginx');
        Config::set('webhook-client.configs.0.repository.branch', 'fake');
    }

    /**
     * Test web hook request fails due to missing content signature header.
     */
    public function testWebhookErrorNoSignature(): void
    {
        $this->app->bind(Handler::class, function () {
            return $this->partialMock(Handler::class, function ($mock) {
                $mock->shouldReceive('report')
                    ->withArgs(WebhookFailed::signingSecretNotSet())
                    ->andReturn();
            });
        });

        $client = (new Client(['base_uri' => config('app.url')]));
        $response = $client->request('POST', route('webhook-client-closed-beta-whitelist', [], false), [
            'body' => json_encode($this->content),
            'verify' => false,
            'http_errors' => false,
        ]);

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * Test web hook request fails due to invalid content signature header.
     */
    public function testWebhookErrorInvalidSignature(): void
    {
        $this->app->bind(Handler::class, function () {
            return $this->partialMock(Handler::class, function ($mock) {
                $mock->shouldReceive('report')
                    ->withArgs(WebhookFailed::invalidSignature())
                    ->andReturn();
            });
        });

        $client = (new Client(['base_uri' => config('app.url')]));
        $response = $client->request('POST', route('webhook-client-closed-beta-whitelist', [], false), [
            'headers' => ['X-Hub-Signature' => 'sha1=' . hash_hmac('sha1', '- fake content', 'secret')],
            'body' => json_encode($this->content),
            'verify' => false,
            'http_errors' => false,
        ]);

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * Test web hook request successful but processing blocked
     * by profile due to not in closed beta.
     */
    public function testWebhookWihoutProcessingNoClosedBeta(): void
    {
        $this->partialMock(UpdateClosedBetaWhitelist::class)
            ->shouldNotReceive('handle');

        $client = (new Client(['base_uri' => config('app.url')]));
        $response = $client->request('POST', route('webhook-client-closed-beta-whitelist', [], false), [
            'headers' => ['X-Hub-Signature' => $this->signature],
            'body' => json_encode($this->content),
            'verify' => false,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody(), json_encode(['message' => 'ok']));
    }

    /**
     * Test web hook request successful but processing blocked
     * by profile due to invalid branch.
     */
    public function testWebhookWihoutProcessingInvalidBranch(): void
    {
        Config::set('webhook-client.configs.0.repository.branch', 'fake-invalid');

        $this->partialMock(UpdateClosedBetaWhitelist::class)
            ->shouldNotReceive('handle');

        $client = (new Client(['base_uri' => config('app.url')]));
        $response = $client->request('POST', route('webhook-client-closed-beta-whitelist', [], false), [
            'headers' => ['X-Hub-Signature' => $this->signature],
            'body' => json_encode($this->content),
            'verify' => false,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody(), json_encode(['message' => 'ok']));
    }

    /**
     * Test web hook request successful and processing launched.
     */
    public function testWebhookSuccessful(): void
    {
        Config::set('wai.closed_beta', true);

        $this->partialMock(UpdateClosedBetaWhitelist::class)
            ->shouldReceive('handle')
            ->andReturn();

        $client = (new Client(['base_uri' => config('app.url')]));
        $response = $client->request('POST', route('webhook-client-closed-beta-whitelist', [], false), [
            'headers' => ['X-Hub-Signature' => $this->signature],
            'body' => json_encode($this->content),
            'verify' => false,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody(), json_encode(['message' => 'ok']));
    }
}
