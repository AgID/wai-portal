<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Events\Jobs\PurgeOrphanedEntitiesCompleted;
use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Purge orphaned entities job.
 */
class PurgeOrphanedEntities implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Analytics Service.
     *
     * @var AnalyticsService
     */
    protected $analyticsService;

    /**
     * Job constructor.
     */
    public function __construct()
    {
        $this->analyticsService = app()->make('analytics-service');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info(
            'Start purging orphaned entities',
            [
                'job' => JobType::PURGE_ORPHANED_ENTITIES,
            ]
        );

        $processedPublicAdministrations = $this->purgePublicAdministrationsWithoutPrimaryWebsite();
        $processedWebsites = $this->purgeOrphanedAnalyticsServiceWebsites();

        event(new PurgeOrphanedEntitiesCompleted([
            'publicAdministrations' => [
                'not_purged' => empty($processedPublicAdministrations->get('not_purged')) ? [] : $processedPublicAdministrations->get('not_purged')->all(),
                'purged' => empty($processedPublicAdministrations->get('purged')) ? [] : $processedPublicAdministrations->get('purged')->all(),
            ],
            'websites' => [
                'not_purged' => empty($processedWebsites->get('not_purged')) ? [] : $processedWebsites->get('not_purged')->all(),
                'purged' => empty($processedWebsites->get('purged')) ? [] : $processedWebsites->get('purged')->all(),
            ],
        ]));
    }

    protected function purgePublicAdministrationsWithoutPrimaryWebsite()
    {
        return PublicAdministration::doesntHave('websites')->get()->mapToGroups(function ($publicAdministration) {
            $abilities = DB::table('abilities')->where('scope', $publicAdministration->id)->get();
            if ($abilities->isNotEmpty()) {
                return [
                    'not_purged' => [
                        'publicAdministration' => $publicAdministration->ipa_code,
                        'reason' => 'Abilities table not empty',
                    ],
                ];
            }

            $assigned_roles = DB::table('assigned_roles')->where('scope', $publicAdministration->id)->get();
            if ($assigned_roles->isNotEmpty()) {
                return [
                    'not_purged' => [
                        'publicAdministration' => $publicAdministration->ipa_code,
                        'reason' => 'Assigned roles table not empty',
                    ],
                ];
            }

            $permissions = DB::table('permissions')->where('scope', $publicAdministration->id)->get();
            if ($permissions->isNotEmpty()) {
                return [
                    'not_purged' => [
                        'publicAdministration' => $publicAdministration->ipa_code,
                        'reason' => 'Permissions table not empty',
                    ],
                ];
            }

            if ($publicAdministration->users->isNotEmpty()) {
                return [
                    'not_purged' => [
                        'publicAdministration' => $publicAdministration->ipa_code,
                        'reason' => 'Public administration has some users',
                    ],
                ];
            }

            $publicAdministration->forceDelete();

            return [
                'purged' => [
                    'publicAdministration' => $publicAdministration->ipa_code,
                ],
            ];
        });
    }

    protected function purgeOrphanedAnalyticsServiceWebsites()
    {
        $publicDashboardRollupId = config('analytics-service.public_dashboard');
        $rollupPortalIds = PublicAdministration::whereNotNull('rollup_id')->get()->pluck('rollup_id');
        $websitesPortalIds = Website::whereNotNull('analytics_id')->get()->pluck('analytics_id');
        $allPortalIds = $websitesPortalIds->merge($rollupPortalIds)->merge([$publicDashboardRollupId])->toArray();
        $allAnalyticsServiceSitesIds = $this->analyticsService->getAllSitesId();
        $orphanedAnalyticsServiceSitesIds = array_diff($allAnalyticsServiceSitesIds, $allPortalIds);

        return collect($orphanedAnalyticsServiceSitesIds)->mapToGroups(function ($orphanedAnalyticsServiceSiteId) use ($orphanedAnalyticsServiceSitesIds) {
            $siteInfo = $this->analyticsService->getSiteFromId($orphanedAnalyticsServiceSiteId);
            $isRollup = 'rollup' === $siteInfo['type'];

            if ($isRollup) {
                $rollupSettings = collect($this->analyticsService->getSiteSettings($orphanedAnalyticsServiceSiteId))->firstWhere('pluginName', 'RollUpReporting');
                $idsInRollup = collect($rollupSettings['settings'] ?? [])->firstWhere('name', 'rollup_idsites')['value'] ?? [];
                rsort($idsInRollup);

                if (!empty($idsInRollup)) {
                    do {
                        $candidatePrimaryWebsite = array_pop($idsInRollup);
                        $candidatePrimaryWebsiteInfo = $this->analyticsService->getSiteFromId($candidatePrimaryWebsite);
                        $foundPrimaryWebsite = 'Sito istituzionale' === $candidatePrimaryWebsiteInfo['name'];
                    } while (!empty($idsInRollup) && !$foundPrimaryWebsite);

                    if ($foundPrimaryWebsite) {
                        $portalWebsite = Website::where('analytics_id', $candidatePrimaryWebsite)->first();
                        // Here we are processing a roll-up site id found in matomo but not present in any public administration.
                        // This roll-up site has primary website so we need to check if the roll-up is is missing from the
                        // public administration of the primary website.
                        $publicAdministrationMissingRollup = $portalWebsite->publicAdministration;
                        if (empty($publicAdministrationMissingRollup->rollup_id)) {
                            $publicAdministrationMissingRollup->rollup_id = $orphanedAnalyticsServiceSiteId;
                            $publicAdministrationMissingRollup->save();

                            return [
                                'not_purged' => [
                                    'website' => $orphanedAnalyticsServiceSiteId,
                                    'reason' => 'The website was a roll-up missing from its public administration, now fixed',
                                ],
                            ];
                        } else {
                            // The public administration of the primary website in the current roll-up site is has already
                            // another roll-up correctly set. It is safe to delete the current orphaned site in matomo.
                            $this->analyticsService->deleteSite($orphanedAnalyticsServiceSiteId);

                            return [
                                'purged' => [
                                    'website' => $orphanedAnalyticsServiceSiteId,
                                    'reason' => 'The website was a roll-up and the corresponding public administration has already another one',
                                ],
                            ];
                        }
                    }

                    return [
                        'not_purged' => [
                            'website' => $orphanedAnalyticsServiceSiteId,
                            'reason' => 'The website is a roll-up but apparently without a primary website (manual check needed)',
                        ],
                    ];
                }

                // This is an empty roll-up site id, safe to delete.
                $this->analyticsService->deleteSite($orphanedAnalyticsServiceSiteId);

                return [
                    'purged' => [
                        'website' => $orphanedAnalyticsServiceSiteId,
                        'reason' => 'The website was a roll-up and was empty',
                    ],
                ];
            } else {
                // Do not delete active (i.e. with tracking data) websites
                if (!$this->analyticsService->isActive($orphanedAnalyticsServiceSiteId)) {
                    $foundSameUrlSiteId = false;
                    $orphanedAnalyticsServiceSiteUrls = $this->analyticsService->getSiteUrlsFromId($orphanedAnalyticsServiceSiteId);

                    // Check if there exists a website with the same URL(s) in matomo
                    foreach ($orphanedAnalyticsServiceSiteUrls as $orphanedAnalyticsServiceSiteUrl) {
                        $sameUrlSitesIds = Arr::pluck($this->analyticsService->getSitesIdFromUrl($orphanedAnalyticsServiceSiteUrl), 'idsite');
                        $sameUrlSitesIds = array_diff($sameUrlSitesIds, $orphanedAnalyticsServiceSitesIds);
                        if (!empty($sameUrlSitesIds)) {
                            foreach ($sameUrlSitesIds as $sameUrlSiteId) {
                                if ($this->analyticsService->isActive($sameUrlSiteId)) {
                                    // We found an active website with the same url of the current orphaned website.
                                    // It is safe to delete the current orphaned site in matomo.
                                    $this->analyticsService->deleteSite($orphanedAnalyticsServiceSiteId);

                                    return [
                                        'purged' => [
                                            'website' => $orphanedAnalyticsServiceSiteId,
                                            'reason' => 'The website was not active and another website is tracking the same url',
                                        ],
                                    ];
                                }
                            }
                        }
                    }

                    return [
                        'not_purged' => [
                            'website' => $orphanedAnalyticsServiceSiteId,
                            'reason' => 'The website was not active but another website tracking the same url was not found (manual check needed)',
                        ],
                    ];
                }

                return [
                    'not_purged' => [
                        'website' => $orphanedAnalyticsServiceSiteId,
                        'reason' => 'The website has tracking data but apparently it is orphaned (manual check needed)',
                    ],
                ];
            }
        });
    }
}
