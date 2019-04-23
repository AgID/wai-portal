<?php

namespace App\Contracts;

use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use Illuminate\Http\RedirectResponse;

/**
 * Analytics Service contract.
 */
interface AnalyticsService
{
    /**
     * Register a new site in the Analytics Service.
     *
     * @param string $siteName the website name
     * @param string $url the website URL
     * @param string $group the website group
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return int the Analytics Service website ID
     */
    public function registerSite(string $siteName, string $url, string $group): int;

    /**
     * Updated an existing site in the Analytics Service.
     *
     * @param string $idSite the Analytics Service website ID
     * @param string $siteName the website name
     * @param string $url the website URL
     * @param string $group the website group
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function updateSite(string $idSite, string $siteName, string $url, string $group, string $tokenAuth): void;

    /**
     * Change archive status in the Analytics Service.
     *
     * @param string $idSites the Analytics Service website ID
     * @param int $status the new status
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     *
     * @see \App\Enums\WebsiteStatus
     */
    public function changeArchiveStatus(string $idSites, int $status, string $tokenAuth): void;

    /**
     * Get Javascript code snippet for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite the Analytics Service website ID
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     *
     * @return string the site tracking code
     */
    public function getJavascriptSnippet(string $idSite, string $tokenAuth): string;

    /**
     * Delete a given site in the Analytics Service.
     *
     * @param string $idSite the Analytics Service website ID
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function deleteSite(string $idSite, string $tokenAuth): void;

    /**
     * Register a new user in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param string $password the Analytics Service user password
     * @param string $email the Analytics Service user email
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function registerUser(string $userLogin, string $password, string $email, string $tokenAuth): void;

    /**
     * Get a specified user in the Analytics Service.
     *
     * @param string $email the Analytics Service user email
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return array the Analytics Service user
     */
    public function getUserByEmail(string $email, string $tokenAuth): array;

    /**
     * @param string $userLogin the Analytics Service user ID
     * @param string $hashedPassword the MD5 hashed Analytics Service user password
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return string the Analytics authentication token
     */
    public function getUserAuthToken(string $userLogin, string $hashedPassword): string;

    /**
     * Delete a specified user in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function deleteUser(string $userLogin, string $tokenAuth): void;

    /**
     * Login and redirect a specified user in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param string $hashedPassword the MD5 hashed Analytics Service user password
     *
     * @return RedirectResponse the Analytics service dashboard
     */
    public function loginAndRedirectUser(string $userLogin, string $hashedPassword): RedirectResponse;

    /**
     * Set permissions for a specified user and specified websites
     * in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param int $access the Analytics Service access level
     * @param string $idSites the Analytics Service website ID
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     *
     * @see \App\Enums\WebsiteAccessType
     */
    public function setWebsiteAccess(string $userLogin, int $access, string $idSites, string $tokenAuth): void;

    /**
     * @param string $idSite the Analytics Service website ID
     * @param int $minutes the minutes period
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return int the live number of website visits
     */
    public function getLiveVisits(string $idSite, int $minutes, string $tokenAuth): int;

    /**
     * Get total number of visits for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite the Analytics Service website ID
     * @param string $from the date range
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return int the total reported website visits
     */
    public function getSiteTotalVisitsFrom(string $idSite, string $from, string $tokenAuth): int;

    /**
     * Get the number of visits for a specified site
     * registered last month in the Analytics Service.
     *
     * @param string $idSite the Analytics Service website ID
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return int the reported website visits from last month
     */
    public function getSiteLastMonthVisits(string $idSite, string $tokenAuth): int;

    /**
     * @param $idSite
     * @param int $days
     * @param string $tokenAuth
     *
     * @throws CommandErrorException
     * @throws AnalyticsServiceException
     *
     * @return array
     */
    public function getSiteLastDaysVisits(string $idSite, int $days, string $tokenAuth): array;
}
