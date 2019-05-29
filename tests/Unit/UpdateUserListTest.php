<?php

namespace Tests\Unit;

use App\Events\Jobs\UserIndexUpdateCompleted;
use App\Jobs\ProcessUsersList;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateUserListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function testUserIndexUpdatedNoUsers(): void
    {
        $job = new ProcessUsersList();
        $job->handle();

        Event::assertDispatched(UserIndexUpdateCompleted::class, function ($event) {
            return empty($event->getInserted())
                && empty($event->getFailed());
        });
    }

    public function testUserIndexUpdatedUserAdded(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $user = factory(User::class)->create();
        $user->publicAdministrations()->sync($publicAdministration->id);

        $job = new ProcessUsersList();
        $job->handle();

        Event::assertDispatched(UserIndexUpdateCompleted::class, function ($event) use ($user) {
            return in_array(['user' => $user->uuid], $event->getInserted(), true)
                && empty($event->getFailed());
        });
    }

    public function testUserIndexUpdatedThrashedUserAdded(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $user = factory(User::class)->create();
        $user->publicAdministrations()->sync($publicAdministration->id);
        $user->delete();

        $job = new ProcessUsersList();
        $job->handle();

        Event::assertDispatched(UserIndexUpdateCompleted::class, function ($event) use ($user) {
            return in_array(['user' => $user->uuid], $event->getInserted(), true)
                && empty($event->getFailed());
        });
    }
}
