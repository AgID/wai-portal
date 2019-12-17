<?php

namespace App\Traits;

use App\Contracts\AnalyticsService;
use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteType;
use App\Exceptions\AnalyticsServiceAccountException;
use App\Models\Website;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasAnalyticsDashboard
{
    public function hasRollUp(): bool
    {
        return !empty($this->rollup_id);
    }

    public function registerRollUp(): void
    {
        $analyticsService = app()->make('analytics-service');
        $this->registerAccount($analyticsService);
        $rollUpId = $analyticsService->registerRollUp($this->name, Arr::pluck($this->websites->all(), 'analytics_id'));
        $analyticsService->setWebsiteAccess($this->ipa_code, WebsiteAccessType::VIEW, $rollUpId);

        //NOTE: RollUp reporting expects user has at least "view" access on every website included in the report
        $analyticsService->setWebsiteAccess($this->ipa_code, WebsiteAccessType::VIEW, $this->websites()->where('type', WebsiteType::PRIMARY)->first()->analytics_id);
        $this->rollup_id = $rollUpId;
        $this->save();
    }

    public function updateRollUp(Website $website): void
    {
        if (empty($this->token_auth)) {
            throw new AnalyticsServiceAccountException('Trying to get auth token from non-existing analytics service account.');
        }

        $analyticsService = app()->make('analytics-service');
        //NOTE: RollUp reporting requires complete websites IDs list on update
        $analyticsService->updateRollUp($this->rollup_id, Arr::pluck($this->websites->all(), 'analytics_id'));

        //NOTE: RollUp reporting expects user has at least "view" access on every website included in the report
        $analyticsService->setWebsiteAccess($this->ipa_code, WebsiteAccessType::VIEW, $website->analytics_id);
    }

    protected function registerAccount(AnalyticsService $analyticsService): void
    {
        $hashedAnalyticsPassword = md5(Str::random(rand(32, 48)) . config('app.salt'));
        $analyticsService->registerUser($this->ipa_code, $hashedAnalyticsPassword, Str::slug($this->ipa_code) . '@' . 'webanalyticsitalia.local');
        $this->token_auth = $analyticsService->getUserAuthToken($this->ipa_code, md5($hashedAnalyticsPassword));
        $this->save();
    }
}
