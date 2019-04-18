<?php

namespace App\Jobs;

use App\Enums\WebsiteStatus;
use App\Events\Jobs\WebsitesMonitoringCheckCompleted;
use App\Events\Website\WebsiteArchived;
use App\Events\Website\WebsiteArchiving;
use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Monitor websites activity job.
 */
class ProcessWebsitesMonitoring implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The authentication token to use for Analytics Service.
     *
     * @var string the authentication token
     */
    protected $tokenAuth;

    /**
     * Job constructor.
     */
    public function __construct()
    {
        $this->tokenAuth = config('analytics-service.admin_token');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $activeWebsites = Website::where('status', WebsiteStatus::ACTIVE)->get();

        $websites = $activeWebsites->mapToGroups(function ($website) {
            try {
                $analyticsService = app()->make('analytics-service');

                $dateWarning = now()->subMonths((int) config('wai.archive_warning'));
                $dateExpire = now()->subMonths((int) config('wai.archive_expiry'));

                if (0 === $analyticsService->getLiveVisits($website->analytics_id, 1440, $this->tokenAuth)) {
                    if ($dateExpire > $website->created_at && 0 === $analyticsService->getSiteTotalVisitsFrom($website->analytics_id, $dateExpire->toDateString(), $this->tokenAuth)) {
                        $website->status = WebsiteStatus::ARCHIVED;

                        $analyticsService->changeArchiveStatus($website->analytics_id, WebsiteStatus::ARCHIVED, $this->tokenAuth);
                        $website->save();

                        event(new WebsiteArchived($website));

                        return [
                            'archived' => [
                                'website' => $website->slug,
                            ],
                        ];
                    }

                    if ($dateWarning > $website->created_at && 0 === $analyticsService->getSiteTotalVisitsFrom($website->analytics_id, $dateWarning->toDateString(), $this->tokenAuth)) {
                        //NOTE: notification will be sent every month (if the difference between warning and
                        //      expire dates is greater than one month) until the website is archived or
                        //      it receives Analytics data
                        event(new WebsiteArchiving($website));

                        return [
                            'archiving' => [
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

        event(new WebsitesMonitoringCheckCompleted(
            empty($websites->get('archived')) ? [] : $websites->get('archived')->all(),
            empty($websites->get('archiving')) ? [] : $websites->get('archiving')->all(),
            empty($websites->get('failed')) ? [] : $websites->get('failed')->all()
        ));
    }
}
