<?php

namespace Tests\Feature;

use App\Exceptions\Handler;
use App\Jobs\UpdateClosedBetaWhitelist;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Spatie\WebhookClient\Exceptions\WebhookFailed;
use Symfony\Component\Yaml\Yaml;
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

        $secret = '256394010059991a71ea05e5d859d2be';
        $this->content = Yaml::dump(['inps', 'inail']);
        $this->signature = hash_hmac('sha256', $this->content, $secret);

        Config::set('wai.closed_beta', false);
        Config::set('webhook-client.configs.0.signing_secret', $secret);
        Config::set('app.url', 'https://nginx');
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
            'body' => $this->content,
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
            'headers' => ['Signature' => hash_hmac('sha256', '- fake content', 'secret')],
            'body' => $this->content,
            'verify' => false,
            'http_errors' => false,
        ]);

        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * Test web hook request successful but processing blocked by profile.
     */
    public function testWebhookWihoutProcessing(): void
    {
        $this->partialMock(UpdateClosedBetaWhitelist::class)
            ->shouldNotReceive('handle');

        $client = (new Client(['base_uri' => config('app.url')]));
        $response = $client->request('POST', route('webhook-client-closed-beta-whitelist', [], false), [
            'headers' => ['Signature' => $this->signature],
            'body' => $this->content,
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
            'headers' => ['Signature' => $this->signature],
            'body' => $this->content,
            'verify' => false,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getBody(), json_encode(['message' => 'ok']));
    }
}
