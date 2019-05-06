<?php

namespace App\Jobs;

use App\Enums\WebsiteStatus;
use App\Events\Jobs\WebsitesMonitoringCheckCompleted;
use App\Events\Website\WebsiteArchived;
use App\Events\Website\WebsiteArchiving;
use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use App\Models\Website;
use Carbon\Carbon;
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
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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

                $daysWarning = (int) config('wai.archive_warning');
                $daysArchive = (int) config('wai.archive_expire');
                $daysDailyNotification = (int) config('wai.archive_warning_daily_notification');
                $notificationDay = (int) config('wai.archive_warning_notification_day');

                // Check:
                // - visits the last 24 hours
                // - website created at least 'archive_expire' days before
                //NOTE: Matomo report contains information for all the requested days, regardless of when the website was created
                if (0 === $analyticsService->getLiveVisits($website->analytics_id, 1440, $this->tokenAuth) && Carbon::now()->subDays($daysArchive)->greaterThanOrEqualTo($website->created_at)) {
                    $visits = $analyticsService->getSiteLastDaysVisits($website->analytics_id, $daysArchive, $this->tokenAuth);

                    $filteredVisits = array_filter($visits, function ($visitCount) {
                        return $visitCount > 0;
                    });

                    if (empty($filteredVisits)) {
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

                    $lastVisit = max(array_keys($filteredVisits));

                    if (Carbon::now()->subDays($daysWarning)->isAfter($lastVisit)) {
                        if (Carbon::now()->dayOfWeek === $notificationDay || Carbon::now()->subDays($daysArchive - $daysDailyNotification)->greaterThanOrEqualTo($lastVisit)) {
                            $daysLeft = $daysArchive - Carbon::now()->diffInDays($lastVisit);

                            event(new WebsiteArchiving($website, $daysLeft));

                            return [
                                'archiving' => [
                                    'website' => $website->slug,
                                ],
                            ];
                        }
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
