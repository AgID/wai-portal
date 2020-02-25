<?php

namespace Tests\Unit;

use App\Events\Jobs\PurgePendingInvitationsCompleted;
use App\Exceptions\CommandErrorException;
use App\Jobs\ProcessUsersIndex;
use App\Jobs\PurgePendingInvitations;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Pending websites check job test.
 */
class PurgePendingInvitationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    /**
     * Test job complete successfully.
     */
    public function testPurgePendingInvitationsCompleted(): void
    {
        $job = new PurgePendingInvitations(true);
        $job->handle();

        Event::assertDispatched(PurgePendingInvitationsCompleted::class);
    }

    /**
     * Test job complete with purged invitations.
     */
    public function testPurgePendingInvitationsPurged(): void
    {
        $user = factory(User::class)->state('invited')->create([
            'created_at' => Carbon::now()->subDays(1 + (int) config('auth.verification.purge')),
        ]);
        $user->registerAnalyticsServiceAccount();

        $job = new PurgePendingInvitations(true);
        $job->handle();

        Event::assertDispatched(PurgePendingInvitationsCompleted::class, function ($event) use ($user) {
            return in_array(['user' => $user->uuid], $event->getPurged(), true)
                && empty($event->getPending());
        });

        $this->expectException(CommandErrorException::class);
        $this->app->make('analytics-service')->getUserByEmail($user->email);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        Event::assertDispatched(ProcessUsersIndex::class);
    }

    /**
     * Test job complete with pending invitations still present.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     */
    public function testPurgePendingInvitationsPending(): void
    {
        $user = factory(User::class)->state('invited')->create();
        $user->registerAnalyticsServiceAccount();

        $job = new PurgePendingInvitations(true);
        $job->handle();

        Event::assertDispatched(PurgePendingInvitationsCompleted::class, function ($event) use ($user) {
            return in_array(['user' => $user->uuid], $event->getPending(), true)
                && empty($event->getPurged());
        });

        $this->assertTrue($user->hasAnalyticsServiceAccount());
        $this->app->make('analytics-service')->getUserByEmail($user->email);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        Event::assertNotDispatched(ProcessUsersIndex::class);
    }
}
