<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Enums\PublicAdministrationStatus;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Events\Jobs\PendingWebsitesCheckCompleted;
use App\Events\PublicAdministration\PublicAdministrationPurged;
use App\Events\Website\WebsiteActivated;
use App\Events\Website\WebsitePurged;
use App\Events\Website\WebsitePurging;
use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use App\Models\Website;
use App\Traits\ActivatesWebsite;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
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
                            $pendingUser = $publicAdministration->users()->where('status', UserStatus::PENDING)->first();
                            if (null !== $pendingUser) {
                                $pendingUser->publicAdministrations()->detach($publicAdministration->id);
                                $pendingUser->deleteAnalyticsServiceAccount();
                                $publicAdministration->forceDelete();
                            }
                            event(new PublicAdministrationPurged($publicAdministration->toJson()));
                        } else {
                            $website->forceDelete();
                        }

                        $analyticsService->deleteSite($website->analytics_id);

                        event(new WebsitePurged($website->toJson()));

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

        event(new PendingWebsitesCheckCompleted(
            empty($websites->get('activated')) ? [] : $websites->get('activated')->all(),
            empty($websites->get('purging')) ? [] : $websites->get('purging')->all(),
            empty($websites->get('purged')) ? [] : $websites->get('purged')->all(),
            empty($websites->get('failed')) ? [] : $websites->get('failed')->all()
        ));
    }
}
