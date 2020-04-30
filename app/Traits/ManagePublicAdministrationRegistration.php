<?php

namespace App\Traits;

use App\Enums\UserRole;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Str;
use Silber\Bouncer\BouncerFacade as Bouncer;

trait ManagePublicAdministrationRegistration
{
    use InteractsWithRedisIndex;

    protected function registerPublicAdministration(User $user, PublicAdministration $publicAdministration, string $url, bool $custom = false): Website
    {
        $analyticsId = app()->make('analytics-service')->registerSite(__('Sito istituzionale'), $url, $publicAdministration->name);

        $publicAdministration->save();
        $website = Website::create([
            'name' => $publicAdministration->name,
            'url' => $url,
            'type' => $custom ? WebsiteType::CUSTOM : WebsiteType::INSTITUTIONAL,
            'public_administration_id' => $publicAdministration->id,
            'analytics_id' => $analyticsId,
            'slug' => Str::slug($url),
            'status' => WebsiteStatus::PENDING,
        ]);

        $publicAdministration->users()->save($user);
        // This is the first time we know which public administration the
        // current user belongs, so we need to set the tenant id just now.
        session()->put('tenant_id', $publicAdministration->id);
        $user->roles()->detach();
        Bouncer::scope()->to($publicAdministration->id);
        $user->assign(UserRole::REGISTERED);
        $user->registerAnalyticsServiceAccount();
        $user->setViewAccessForWebsite($website);
        $user->syncWebsitesPermissionsToAnalyticsService();

        return $website;
    }

    protected function checkIsNotPrimary(string $url): bool
    {
        $publicAdministration = $this->getPublicAdministrationEntryByPrimaryWebsiteUrl($url);

        if (empty($publicAdministration)) {
            return true;
        }

        $publicAdministrationPrimaryWebsiteHost = Str::slug(preg_replace('/^http(s)?:\/\/(www\.)?(.+)$/i', '$3', $publicAdministration['site']));
        $inputHost = Str::slug(preg_replace('/^http(s)?:\/\/(www\.)?(.+)$/i', '$3', $url));

        return $publicAdministrationPrimaryWebsiteHost !== $inputHost;
    }
}
