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
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function updateSite(string $idSite, string $siteName, string $url, string $group): void;

    /**
     * Get an array with the ids of all the sites in the Analytics Service.
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     *
     * @return array all the ids of all the sites
     */
    public function getAllSitesId(): array;

    /**
     * Get the id of the site with this url in the Analytics Service.
     *
     * @param string $siteUrl the site url of the website
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     *
     * @return array all the ids of all the sites
     */
    public function getSitesIdFromUrl(string $siteUrl): array;

    /**
     * Change archive status in the Analytics Service.
     *
     * @param string $idSites the Analytics Service website ID
     * @param int $status the new status
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     *
     * @see \App\Enums\WebsiteStatus
     */
    public function changeArchiveStatus(string $idSites, int $status): void;

    /**
     * Get Javascript code snippet for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite the Analytics Service website ID
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     *
     * @return string the site tracking code
     */
    public function getJavascriptSnippet(string $idSite): string;

    /**
     * Delete a given site in the Analytics Service.
     *
     * @param string $idSite the Analytics Service website ID
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function deleteSite(string $idSite): void;

    /**
     * Register a new user in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param string $password the Analytics Service user password
     * @param string $email the Analytics Service user email
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function registerUser(string $userLogin, string $password, string $email): void;

    /**
     * Get a specified user in the Analytics Service.
     *
     * @param string $email the Analytics Service user email
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return array the Analytics Service user
     */
    public function getUserByEmail(string $email): array;

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
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function deleteUser(string $userLogin): void;

    /**
     * Update the email address of a specified user in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param string $updatedEmail the updated email address
     * @param string $passwordConfirmation the user password needed to confirm the email change
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function updateUserEmail(string $userLogin, string $updatedEmail, string $passwordConfirmation, string $tokenAuth): void;

    /**
     * Get an array with the login strings of all the users in the Analytics Service.
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     *
     * @return array all the login strings of all the users
     */
    public function getUsersLogin(): array;

    /**
     * Login and redirect a specified user in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param string $hashedPassword the MD5 hashed Analytics Service user password
     * @param string|null $idSite the Analytics Service website ID to redirect to
     *
     * @return RedirectResponse the Analytics service dashboard
     */
    public function loginAndRedirectUser(string $userLogin, string $hashedPassword, string $idSite = null): RedirectResponse;

    /**
     * Set permissions for a specified user and specified websites
     * in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param int $access the Analytics Service access level
     * @param string $idSites the Analytics Service website ID
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     *
     * @see \App\Enums\WebsiteAccessType
     */
    public function setWebsiteAccess(string $userLogin, int $access, string $idSites): void;

    /**
     * @param string $idSite the Analytics Service website ID
     * @param int $minutes the minutes period
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return int the live number of website visits
     */
    public function getLiveVisits(string $idSite, int $minutes): int;

    /**
     * Get total number of visits for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite the Analytics Service website ID
     * @param string $from the date range
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return int the total reported website visits
     */
    public function getSiteTotalVisitsFrom(string $idSite, string $from): int;

    /**
     * Get the number of visits for a specified site
     * registered last month in the Analytics Service.
     *
     * @param string $idSite the Analytics Service website ID
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return int the reported website visits from last month
     */
    public function getSiteLastMonthVisits(string $idSite): int;

    /**
     * Get the daily number of visits for the last requested days.
     *
     * @param string $idSite the Analytics Service website ID
     * @param int $days the requested number of days
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return array the list of days with the number of visits
     */
    public function getSiteLastDaysVisits(string $idSite, int $days): array;

    /**
     * Get the stats from a specific url.
     *
     * @param string $idSite the Analytics Service website ID
     * @param int $days the requested number of days
     * @param string $url the url of the site
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return array the list of days with the number of visits
     */
    public function getStatsPageUrl(string $idSite, int $days, string $url): array;

    /**
     * Get all defined segments.
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return array the list of days with the number of visits
     */
    public function getAllSegments(): array;

    /**
     * Add segment for a specific definition.
     *
     * @param string $idSite the Analytics Service website ID
     * @param string $segment the definition of the segment
     * @param string $name the name of the segment
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return array the list of days with the number of visits
     */
    public function addSegment(string $idSite, string $segment, string $name): void;

    /**
     * Delete a specified segment.
     *
     * @param string $idSegment the segment to be deleted
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     */
    public function deleteSegment(string $idSegment): void;

    /**
     * Get the country report for a specific segment in a specified month.
     *
     * @param string $idSite the Analytics Service website ID
     * @param string $date a date in the month
     * @param string $segment the segment of the report
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return array the report with the countries detected for the specific segment
     */
    public function getCountriesInSegmentInMonth(string $idSite, string $date, string $segment): array;

    /**
     * Register a new analytics service report.
     *
     * @param string $name the report name
     * @param array $idSites the Analytics Service websites IDs
     *
     * @return int the Analytics Service report ID
     */
    public function registerRollUp(string $name, array $idSites): int;

    /**
     * Update an existing analytics service report.
     * NOTE: the given Analytics Service websites IDs list will replace
     *       the current one.
     *
     * @param string $idRollUp the Analytics Service report ID
     * @param array $idSites the Analytics Service websites IDs
     */
    public function updateRollUp(string $idRollUp, array $idSites): void;
}
