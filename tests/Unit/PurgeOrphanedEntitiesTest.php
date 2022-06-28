<?php

namespace Tests\Unit;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Events\Jobs\PurgeOrphanedEntitiesCompleted;
use App\Jobs\PurgeOrphanedEntities;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Websites activity check job tests.
 */
class PurgeOrphanedEntitiesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        Bouncer::dontCache();
    }

    /**
     * Test job completed successfully.
     */
    public function testPurgeOrphanedEntitiesCompleted(): void
    {
        $job = new PurgeOrphanedEntities();
        $job->handle();

        Event::assertDispatched(PurgeOrphanedEntitiesCompleted::class, function ($event) {
            return empty($event->getProcessed()['publicAdministrations']['purged'])
                && empty($event->getProcessed()['publicAdministrations']['not_purged']);
        });
    }

    /**
     * Test job complete with purged public administration.
     */
    public function testPurgedPublicAdministration(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();

        $job = new PurgeOrphanedEntities();
        $job->handle();

        Event::assertDispatched(PurgeOrphanedEntitiesCompleted::class, function ($event) use ($publicAdministration) {
            return !empty($event->getProcessed()['publicAdministrations']['purged'])
                && in_array(['publicAdministration' => $publicAdministration->ipa_code], $event->getProcessed()['publicAdministrations']['purged'], true)
                && empty($event->getProcessed()['publicAdministrations']['not_purged']);
        });
    }

    /**
     * Test job complete with not purged public administration having users.
     */
    public function testNotPurgedPublicAdministrationWithUsers(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $user = factory(User::class)->state('pending')->create();
        $publicAdministration->users()->sync($user->id);

        $job = new PurgeOrphanedEntities();
        $job->handle();

        Event::assertDispatched(PurgeOrphanedEntitiesCompleted::class, function ($event) use ($publicAdministration) {
            return !empty($event->getProcessed()['publicAdministrations']['not_purged'])
                && in_array([
                    'publicAdministration' => $publicAdministration->ipa_code,
                    'reason' => 'Public administration has some users',
                ], $event->getProcessed()['publicAdministrations']['not_purged'], true)
                && empty($event->getProcessed()['publicAdministrations']['purged']);
        });
    }

    /**
     * Test job complete withnot purged public administration having roles.
     */
    public function testNotPurgedPublicAdministrationWithAssignedRoles(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $user = factory(User::class)->state('pending')->create();
        $publicAdministration->users()->sync($user->id);
        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
            $user->assign(UserRole::ADMIN);
        });
        $publicAdministration->users()->detach();

        $job = new PurgeOrphanedEntities();
        $job->handle();

        Event::assertDispatched(PurgeOrphanedEntitiesCompleted::class, function ($event) use ($publicAdministration) {
            return !empty($event->getProcessed()['publicAdministrations']['not_purged'])
                && in_array([
                    'publicAdministration' => $publicAdministration->ipa_code,
                    'reason' => 'Assigned roles table not empty',
                ], $event->getProcessed()['publicAdministrations']['not_purged'], true)
                && empty($event->getProcessed()['publicAdministrations']['purged']);
        });
    }

    /**
     * Test job complete with not purged public administration having permissions.
     */
    public function testNotPurgedPublicAdministrationWithPermissions(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $user = factory(User::class)->state('pending')->create();
        $publicAdministration->users()->sync($user->id);
        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
            $user->allow(UserPermission::MANAGE_WEBSITES);
        });
        $publicAdministration->users()->detach();

        $job = new PurgeOrphanedEntities();
        $job->handle();

        Event::assertDispatched(PurgeOrphanedEntitiesCompleted::class, function ($event) use ($publicAdministration) {
            return !empty($event->getProcessed()['publicAdministrations']['not_purged'])
                && in_array([
                    'publicAdministration' => $publicAdministration->ipa_code,
                    'reason' => 'Permissions table not empty',
                ], $event->getProcessed()['publicAdministrations']['not_purged'], true)
                && empty($event->getProcessed()['publicAdministrations']['purged']);
        });
    }
}
