<?php

namespace App\Traits;

use App\Contracts\AnalyticsService;
use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteType;
use App\Models\Website;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Public administration dashboard user management.
 */
trait HasAnalyticsDashboard
{
    /**
     * Check public administration has a dashboard report.
     *
     * @return bool true if has a report, false otherwise
     */
    public function hasRollUp(): bool
    {
        return !empty($this->rollup_id);
    }

    /**
     * Register the analytics report for the public administration.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     */
    public function registerRollUp(): void
    {
        $analyticsService = app()->make('analytics-service');
        //NOTE: if RollUp reporting plugin doesn't exists, we allow a CommandException to be thrown
        //      to break public administration dashboard user/permissions management
        $rollUpId = $analyticsService->registerRollUp($this->name, Arr::pluck($this->websites->all(), 'analytics_id'));
        $this->registerAccount($analyticsService);
        $analyticsService->setWebsiteAccess($this->ipa_code, WebsiteAccessType::VIEW, $rollUpId);

        //NOTE: RollUp reporting expects user has at least "view" access on every website included in the report
        $analyticsService->setWebsiteAccess($this->ipa_code, WebsiteAccessType::VIEW, $this->websites()->where('type', WebsiteType::INSTITUTIONAL)->first()->analytics_id);
        $this->rollup_id = $rollUpId;
        $this->save();
    }

    /**
     * Add the specified website to an existing analytics report for the public administration.
     *
     * @param Website $website the website to add into public administration rollup report
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     */
    public function addToRollUp(Website $website): void
    {
        if (empty($this->rollup_id)) {
            //NOTE: return immediately since the public administration
            //      doesn't have a RollUp report (no plugin installed)
            return;
        }

        $analyticsService = app()->make('analytics-service');
        //NOTE: RollUp reporting requires complete websites IDs list on update
        $analyticsService->updateRollUp($this->rollup_id, Arr::pluck($this->websites->all(), 'analytics_id'));

        //NOTE: RollUp reporting expects user has at least "view" access on every website included in the report
        $analyticsService->setWebsiteAccess($this->ipa_code, WebsiteAccessType::VIEW, $website->analytics_id);
    }

    /**
     * Register an analytics account for the public administration.
     *
     * @param AnalyticsService $analyticsService the analytics service
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     */
    protected function registerAccount(AnalyticsService $analyticsService): void
    {
        $hashedAnalyticsPassword = md5(Str::random(rand(32, 48)) . config('app.salt'));
        $analyticsService->registerUser($this->ipa_code, $hashedAnalyticsPassword, Str::slug($this->ipa_code) . '@' . 'webanalyticsitalia.local');
        $this->token_auth = $analyticsService->getUserAuthToken($this->ipa_code, md5($hashedAnalyticsPassword));
        $this->save();
    }
}
