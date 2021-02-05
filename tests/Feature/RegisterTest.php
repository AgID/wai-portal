<?php

namespace Tests\Feature;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Italia\SPIDAuth\SPIDUser;
use Tests\TestCase;

/**
 * User registration test.
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pre-test setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    /**
     * Test registration successful.
     */
    public function testRegistrationSuccessful(): void
    {
        $spidUser = new SPIDUser([
            'familyName' => 'Rossi',
            'name' => 'Mario',
            'fiscalNumber' => 'AAABBB90E09R123D',
            'spidCode' => 'spidcode',
        ]);

        $this->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $spidUser,
            ])
            ->post(route('auth.register'), [
                'email' => 'new@webanalytics.italia.it',
                'accept_terms' => 'on',
            ])
            ->assertSessionDoesntHaveErrors([
                'email',
                'accept_terms',
            ])
            ->assertRedirect(route('verification.notice'));

        Event::assertDispatched(Registered::class, function ($event) {
            return 'new@webanalytics.italia.it' === $event->user->email;
        });
    }

    /**
     * Test registration fail due to data validation.
     */
    public function testRegistrationFailValidation(): void
    {
        $this->withSession([
                'spid_sessionIndex' => 'fake-session-index',
            ])
            ->post(route('auth.register'))
            ->assertSessionHasErrors([
                'accept_terms',
            ])
            ->assertRedirect(route('home'));

        Event::assertNotDispatched(Registered::class);
    }
}
