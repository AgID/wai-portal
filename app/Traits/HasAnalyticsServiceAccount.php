<?php

namespace App\Traits;

use App\Exceptions\AnalyticsServiceAccountException;
use Illuminate\Support\Str;

trait HasAnalyticsServiceAccount
{
    /**
     * Check if this user is registered in the Analytics Service.
     *
     * @return bool true if this user is registered in the Analytics Service, false otherwise
     */
    public function hasAnalyticsServiceAccount(): bool
    {
        return !empty($this->partial_analytics_password);
    }

    /**
     * Get the Analytics Service account auth token for this user.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\AnalyticsServiceAccountException if the Analytics Service account doesn't exist
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     *
     * @return string the auth token
     */
    public function getAnalyticsServiceAccountTokenAuth(): string
    {
        if (!$this->hasAnalyticsServiceAccount()) {
            throw new AnalyticsServiceAccountException('Trying to get auth token from non-existing analytics service account.');
        }

        return app()->make('analytics-service')->getUserAuthToken($this->uuid, md5($this->analytics_password));
    }

    /**
     * Register this user in the Analytics Service.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function registerAnalyticsServiceAccount(): void
    {
        $this->partial_analytics_password = Str::random(rand(32, 48));
        $this->save();
        app()->make('analytics-service')->registerUser($this->uuid, $this->analytics_password, $this->email);
    }

    /**
     * Update this user's email address in the Analytics Service account.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \App\Exceptions\AnalyticsServiceAccountException if the Analytics Service account doesn't exist
     */
    public function updateAnalyticsServiceAccountEmail(): void
    {
        app()->make('analytics-service')->updateUserEmail($this->uuid, $this->email, $this->analytics_password);
    }

    /**
     * Register this user in the Analytics Service.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function deleteAnalyticsServiceAccount(): void
    {
        $this->partial_analytics_password = null;
        $this->save();
        app()->make('analytics-service')->deleteUser($this->uuid);
    }
}
