<?php

namespace App\Traits;

use App\Enums\UserPermission;
use App\Enums\WebsiteAccessType;
use App\Events\User\UserWebsiteAccessChanged;
use App\Exceptions\TenantIdNotSetException;
use App\Models\Website;
use Silber\Bouncer\BouncerFacade as Bouncer;

trait HasWebsitePermissions
{
    /**
     * Set no-access permission for this user and for the specified website
     * in the Analytics Service.
     *
     * @param Website $website the website for which the permission is set
     *
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     */
    public function setNoAccessForWebsite(Website $website): void
    {
        $this->ensurePermissionScopeIsSet();
        Bouncer::allow($this)->to(UserPermission::NO_ACCESS, $website);
        Bouncer::disallow($this)->to(UserPermission::READ_ANALYTICS, $website);
        Bouncer::disallow($this)->to(UserPermission::MANAGE_ANALYTICS, $website);
        Bouncer::refreshFor($this);

        event(new UserWebsiteAccessChanged($this, $website, WebsiteAccessType::NO_ACCESS()));
    }

    /**
     * Set view permission for this user and for the specified website
     * in the Analytics Service.
     *
     * @param Website $website the website for which the permission is set
     *
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     */
    public function setViewAccessForWebsite(Website $website): void
    {
        $this->ensurePermissionScopeIsSet();
        Bouncer::allow($this)->to(UserPermission::READ_ANALYTICS, $website);
        Bouncer::disallow($this)->to(UserPermission::NO_ACCESS, $website);
        Bouncer::disallow($this)->to(UserPermission::MANAGE_ANALYTICS, $website);
        Bouncer::refreshFor($this);

        event(new UserWebsiteAccessChanged($this, $website, WebsiteAccessType::VIEW()));
    }

    /**
     * Set write permission for this user and for the specified website
     * in the Analytics Service.
     *
     * @param Website $website the website for which the permission is set
     *
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     */
    public function setWriteAccessForWebsite(Website $website): void
    {
        $this->ensurePermissionScopeIsSet();
        Bouncer::allow($this)->to(UserPermission::READ_ANALYTICS, $website);
        Bouncer::allow($this)->to(UserPermission::MANAGE_ANALYTICS, $website);
        Bouncer::disallow($this)->to(UserPermission::NO_ACCESS, $website);
        Bouncer::refreshFor($this);

        event(new UserWebsiteAccessChanged($this, $website, WebsiteAccessType::WRITE()));
    }

    /**
     * Set admin permission for this user and for the specified website
     * in the Analytics Service.
     *
     * @param Website $website the website for which the permission is set
     *
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     */
    public function setAdminAccessForWebsite(Website $website): void
    {
        $this->ensurePermissionScopeIsSet();
        Bouncer::allow($this)->to(UserPermission::READ_ANALYTICS, $website);
        Bouncer::allow($this)->to(UserPermission::MANAGE_ANALYTICS, $website);
        Bouncer::disallow($this)->to(UserPermission::NO_ACCESS, $website);
        Bouncer::refreshFor($this);

        event(new UserWebsiteAccessChanged($this, $website, WebsiteAccessType::ADMIN()));
    }

    /**
     * Synchronize current user website permission to the analytics service.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function syncWebsitesPermissionsToAnalyticsService(): void
    {
        $this->ensurePermissionScopeIsSet();
        $this->getAbilities()->where('entity_type', 'App\Models\Website')->map(function ($websitePermission) {
            $website = Website::find($websitePermission->entity_id);
            app()->make('analytics-service')->setWebsiteAccess($this->uuid, WebsiteAccessType::fromUserPermission($websitePermission->name), $website->analytics_id, config('analytics-service.admin_token'));
        });
    }

    /**
     * Ensure that the tenant id and the permission scope is correctly set in
     * the current session.
     *
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     */
    private function ensurePermissionScopeIsSet(): void
    {
        if (empty(session('tenant_id'))) {
            throw new TenantIdNotSetException();
        }

        Bouncer::scope()->to(session('tenant_id'));
    }
}
