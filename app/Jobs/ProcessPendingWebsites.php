<?php

namespace App\Jobs;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Silber\Bouncer\BouncerFacade as Bouncer;

class ProcessPendingWebsites implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pendingWebsites = Website::where('status', WebsiteStatus::PENDING)->get();
        $pendingWebsites->map(function ($website) {
            if ($website->getTotalVisits() > 0) {
                $analyticsService = app()->make('analytics-service');
                $publicAdministration = $website->publicAdministration;

                logger()->info('New website "' . $website->name . '" activated [' . $website->url . ']'); //TODO: notify me and the user!

                $website->status = WebsiteStatus::ACTIVE;
                $website->save();

                if (PublicAdministrationStatus::PENDING == $publicAdministration->status) {
                    $publicAdministration->status = PublicAdministrationStatus::ACTIVE;
                    $publicAdministration->save();

                    logger()->info('New public administration activated [' . $publicAdministration->name . ']'); //TODO: notify me and the user!

                    $pendingUser = $publicAdministration->users()->where('status', UserStatus::PENDING)->first();

                    if ($pendingUser) {
                        $pendingUser->partial_analytics_password = Str::random(rand(32, 48));
                        $pendingUser->status = UserStatus::ACTIVE;
                        $pendingUser->save();
                        $pendingUser->roles()->detach();
                        Bouncer::scope()->to($publicAdministration->id);
                        $pendingUser->assign('admin');
                        $analyticsService->registerUser($pendingUser->email, $pendingUser->analytics_password, $pendingUser->email);

                        logger()->info('User ' . $pendingUser->getInfo() . ' was activated and registered in the Analytics Service.'); //TODO: notify me and the user!
                    }
                }

                foreach ($publicAdministration->users as $user) {
                    $access = $user->can('manage-analytics') ? 'admin' : 'view';
                    $analyticsService->setWebsitesAccess($user->email, $access, $website->analytics_id);

                    logger()->info('User ' . $user->getInfo() . ' was granted with "' . $access . '" access in the Analytics Service.'); //TODO: notify me and the user!
                }
            } elseif ($website->created_at->diffInDays(Carbon::now()) > 15) {
                $publicAdministration = $website->publicAdministration;

                if (PublicAdministrationStatus::PENDING == $publicAdministration->status) {
                    $pendingUser = $publicAdministration->users()->where('status', UserStatus::PENDING)->first();
                    $pendingUser->publicAdministrations()->detach($publicAdministration->id);
                    $pendingUser->save();
                    $publicAdministration->forceDelete();
                    logger()->info('Website "' . $website->name . '" [' . $website->url . '] was deleted as not activated within 15 days'); //TODO: notify me and the user!
                    logger()->info('Public administration [' . $publicAdministration->name . '] was deleted as not activated within 15 days');
                } else {
                    $website->forceDelete();
                    logger()->info('Website "' . $website->name . '" [' . $website->url . '] was deleted as not activated within 15 days'); //TODO: notify me and the user!
                }
            }
        });
    }
}
