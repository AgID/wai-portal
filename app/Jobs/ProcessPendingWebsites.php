<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Enums\PublicAdministrationStatus;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Events\Jobs\PendingWebsitesCheckCompleted;
use App\Events\PublicAdministration\PublicAdministrationPurged;
use App\Events\Website\WebsiteActivated;
use App\Events\Website\WebsitePurged;
use App\Events\Website\WebsitePurging;
use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use App\Traits\ActivatesWebsite;
use App\Traits\ManageRecipientNotifications;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Check pending websites state job.
 */
class ProcessPendingWebsites implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use ActivatesWebsite;
    use ManageRecipientNotifications;

    /**
     * Purge check flag.
     *
     * @var bool the flag
     */
    public $executePurgeCheck;

    /**
     * Job constructor.
     *
     * @param bool $executePurgeCheck true to execute purge check, false otherwise
     */
    public function __construct(bool $executePurgeCheck = false)
    {
        $this->executePurgeCheck = $executePurgeCheck;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info(
            'Processing pending websites',
            [
                'job' => JobType::PROCESS_PENDING_WEBSITES,
            ]
        );

        $pendingWebsites = Website::where('status', WebsiteStatus::PENDING)->get();

        $websites = $pendingWebsites->mapToGroups(function ($website) {
            try {
                $analyticsService = app()->make('analytics-service');
                if ($this->hasActivated($website)) {
                    $this->activate($website);

                    event(new WebsiteActivated($website));

                    return [
                        'activated' => [
                            'website' => $website->slug,
                        ],
                    ];
                }

                // NOTE: job is dispatched hourly but purge check must be executed
                //       only once a day to avoid 'purging' notification spam
                if ($this->executePurgeCheck) {
                    if ((int) config('wai.purge_warning') === $website->created_at->diffInDays(Carbon::now())) {
                        event(new WebsitePurging($website));

                        return [
                            'purging' => [
                                'website' => $website->slug,
                            ],
                        ];
                    }

                    if ($website->created_at->diffInDays(Carbon::now()) > (int) config('wai.purge_expiry')) {
                        $publicAdministration = $website->publicAdministration;

                        if ($publicAdministration->status->is(PublicAdministrationStatus::PENDING)) {
                            $pendingUser = $publicAdministration->users()->where('user_status', UserStatus::PENDING)->first();
                            $pendingUser->publicAdministrations()->detach($publicAdministration->id);
                            $userEmailForPurgedPublicAdministration = $this->getUserEmailForPublicAdministration($pendingUser, $publicAdministration);

                            if ($pendingUser->publicAdministrations->isEmpty()) {
                                $pendingUser->deleteAnalyticsServiceAccount();
                            }

                            $publicAdministration->forceDelete();
                            event(new PublicAdministrationPurged($publicAdministration->toJson(), $pendingUser, $userEmailForPurgedPublicAdministration));
                        } else {
                            $website->forceDelete();
                        }

                        $analyticsService->deleteSite($website->analytics_id);

                        event(new WebsitePurged($website->toJson(), $publicAdministration->toJson()));

                        return [
                            'purged' => [
                                'website' => $website->slug,
                            ],
                        ];
                    }
                }
            } catch (BindingResolutionException $exception) {
                report($exception);

                return [
                    'failed' => [
                        'website' => $website->slug,
                        'reason' => 'Unable to bind to Analytics Service',
                    ],
                ];
            } catch (AnalyticsServiceException $exception) {
                report($exception);

                return [
                    'failed' => [
                        'website' => $website->slug,
                        'reason' => 'Unable to contact the Analytics Service',
                    ],
                ];
            } catch (CommandErrorException $exception) {
                report($exception);

                return [
                    'failed' => [
                        'website' => $website->slug,
                        'reason' => 'Invalid command for Analytics Service',
                    ],
                ];
            }

            return [
                'ignored' => [
                    'website' => $website->slug,
                ],
            ];
        });

        // Fix active public administrations with null rollup_id
        PublicAdministration::whereNull('rollup_id')->get()->each(function ($publicAdministration) use ($pendingWebsites, $websites) {
            if ($publicAdministration->status->is(PublicAdministrationStatus::ACTIVE)) {
                $primaryWebsite = $publicAdministration->websites()->where('type', WebsiteType::INSTITUTIONAL)->first();
                if (is_null($primaryWebsite) || $pendingWebsites->contains($primaryWebsite)) {
                    // Do not try to re-activate publicAdministrations processed in this job instance
                    // since the activation process may still be in the queue
                    return;
                }
                if ($this->hasActivated($primaryWebsite)) {
                    // Change public administration status to PENDING, in order to retry activation
                    $publicAdministration->status = PublicAdministrationStatus::PENDING;
                    $publicAdministration->save();

                    $this->activate($primaryWebsite);

                    $activatedWebsites = $websites->get('activated') ?? collect([]);
                    $activatedWebsites->concat([
                        'website' => $primaryWebsite->slug,
                        'reason' => 'Re-activated public administration with null rollup',
                    ]);

                    $websites->merge(['activated' => $activatedWebsites]);
                }
            }
        });

        // Fix unactivated users
        User::where('status', UserStatus::PENDING)->has('activePublicAdministrations')->get()->each(function ($unactivatedUser) {
            $unactivatedUser->status = UserStatus::ACTIVE;
            $unactivatedUser->save();
        });

        // Fix pending public administrations with institutional active website
        $activeWebsitesInPendingPublicAdministration = Website::where('type', WebsiteType::INSTITUTIONAL)
            ->where('status', WebsiteStatus::ACTIVE)->whereHas('publicAdministration', function (Builder $query) {
                $query->where('status', PublicAdministrationStatus::PENDING);
            })->get()->each(function ($activeWebsiteInPendingPublicAdministration) use ($pendingWebsites, $websites) {
                if ($pendingWebsites->contains($activeWebsiteInPendingPublicAdministration)) {
                    // Do not try to re-activate publicAdministrations processed in this job instance
                    // since the activation process may still be in the queue
                    return;
                }

                $this->activate($activeWebsiteInPendingPublicAdministration);

                $activatedWebsites = $websites->get('activated') ?? collect([]);
                $activatedWebsites->concat([
                    'website' => $activeWebsiteInPendingPublicAdministration->slug,
                ]);

                $websites->merge(['activated' => $activatedWebsites]);
            });

        event(new PendingWebsitesCheckCompleted(
            empty($websites->get('activated')) ? [] : $websites->get('activated')->all(),
            empty($websites->get('purging')) ? [] : $websites->get('purging')->all(),
            empty($websites->get('purged')) ? [] : $websites->get('purged')->all(),
            empty($websites->get('failed')) ? [] : $websites->get('failed')->all()
        ));
    }
}
