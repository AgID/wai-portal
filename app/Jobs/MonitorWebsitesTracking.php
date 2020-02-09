<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Events\Jobs\WebsitesMonitoringCheckCompleted;
use App\Events\Website\PrimaryWebsiteNotTracking;
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
class MonitorWebsitesTracking implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info(
            'Monitoring websites tracking status',
            [
                'job' => JobType::MONITOR_WEBSITES_TRACKING,
            ]
        );

        $activeWebsites = Website::where('status', WebsiteStatus::ACTIVE)->get();

        $websites = $activeWebsites->mapToGroups(function ($website) {
            try {
                $analyticsService = app()->make('analytics-service');

                $intervalWarning = (int) config('wai.archive_warning');
                $intervalArchive = (int) config('wai.archive_expire');
                $daysDailyNotification = (int) config('wai.archive_warning_daily_notification');
                $notificationInterval = (int) config('wai.archive_warning_notification_interval');
                $notificationDay = (int) config('wai.primary_website_not_tracking_notification_day');

                // Check:
                // - visits the last 24 hours
                // - website created at least 'archive_expire' days before
                //NOTE: Matomo report contains information for all the requested days, regardless of when the website was created
                if (0 === $analyticsService->getLiveVisits($website->analytics_id, 1440) && Carbon::now()->subDays($intervalArchive)->greaterThanOrEqualTo($website->created_at)) {
                    $visits = $analyticsService->getSiteLastDaysVisits($website->analytics_id, $intervalArchive);

                    $filteredVisits = array_filter($visits, function ($visitCount) {
                        return $visitCount > 0;
                    });

                    if (empty($filteredVisits)) {
                        // NOTE: primary website cannot be archived
                        if ($website->type->is(WebsiteType::PRIMARY)) {
                            // NOTE: prevent daily notifications spam
                            if (Carbon::now()->isoWeekday() === $notificationDay) {
                                event(new PrimaryWebsiteNotTracking($website));

                                return [
                                    'archiving' => [
                                        'website' => $website->slug,
                                    ],
                                ];
                            }

                            return [
                                'ignored' => [
                                    'website' => $website->slug,
                                ],
                            ];
                        }

                        $website->status = WebsiteStatus::ARCHIVED;
                        $analyticsService->changeArchiveStatus($website->analytics_id, WebsiteStatus::ARCHIVED);
                        $website->save();

                        event(new WebsiteArchived($website));

                        return [
                            'archived' => [
                                'website' => $website->slug,
                            ],
                        ];
                    }

                    $lastVisit = max(array_keys($filteredVisits));

                    if (Carbon::now()->subDays($intervalWarning)->greaterThanOrEqualTo($lastVisit)) {
                        $daysLeftBeforeArchiving = Carbon::now()->diffInDays($lastVisit);

                        if ((0 === ($daysLeftBeforeArchiving - $intervalWarning) % $notificationInterval) || (Carbon::now()->subDays($intervalArchive - $daysDailyNotification)->greaterThanOrEqualTo($lastVisit) && !$website->type->is(WebsiteType::PRIMARY))) {
                            $website->type->is(WebsiteType::PRIMARY) ? event(new PrimaryWebsiteNotTracking($website)) : event(new WebsiteArchiving($website, $daysLeftBeforeArchiving));

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
