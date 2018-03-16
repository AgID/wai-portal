<?php

namespace App\Jobs;

use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        $pendingWebsites = Website::where('status', 'pending')->get();
        $pendingWebsites->map(function ($website) {
            if ($website->getTotalVisits() > 0) {
                $analyticsService = app()->make('analytics-service');

                logger()->info('New website activated ['.$website->url.']'); //TODO: notify me and the user!

                $website->status = 'active';
                $website->save();

                if ($website->publicAdministration->status == 'pending') {
                    $website->publicAdministration->status = 'active';
                    $website->publicAdministration->save();

                    logger()->info('New public administration activated ['.$website->publicAdministration->name.']'); //TODO: notify me and the user!

                    $pendingUser = $website->publicAdministration->users()->where('status', 'pending')->first();

                    if ($pendingUser) {
                        $pendingUser->analytics_password = str_random(20);
                        $pendingUser->status = 'active';
                        $pendingUser->save();
                        $pendingUser->roles()->detach();
                        $pendingUser->assign('admin');
                        $analyticsService->registerUser($pendingUser->email, $pendingUser->analytics_password, $pendingUser->email);

                        logger()->info('User '.$pendingUser->getInfo().' was activated and registered in the Analytics Service.'); //TODO: notify me and the user!
                    }
                }

                foreach ($website->publicAdministration->users as $user) {
                    $access = $user->can('manage-analytics') ? 'admin' : 'view';
                    $analyticsService->setWebsitesAccess($user->email, $access, $website->analytics_id);

                    logger()->info('User '.$user->getInfo().' was granted with "'.$access.'" access in the Analytics Service.'); //TODO: notify me and the user!
                }
            } else if ($website->created_at->diffInDays(Carbon::now()) > 15) {
                if ($website->publicAdministration->status == 'pending') {
                    $pendingUser = $website->publicAdministration->users()->where('status', 'pending')->first();
                    $pendingUser->publicAdministration()->dissociate();
                    $pendingUser->save();
                    $website->publicAdministration->forceDelete();
                    logger()->info('Website ['.$website->url.'] was deleted as not activated within 15 days'); //TODO: notify me and the user!
                    logger()->info('Public administration ['.$website->publicAdministration->name.'] was deleted as not activated within 15 days');
                } else {
                    $website->forceDelete();
                    logger()->info('Website ['.$website->url.'] was deleted as not activated within 15 days'); //TODO: notify me and the user!
                }
            }
        });

    }
}
