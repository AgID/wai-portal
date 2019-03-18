<?php

namespace App\Contracts;

use App\Exceptions\AnalyticsServiceException;

interface AnalyticsService
{
    /**
     * Register a new site in the Analytics Service.
     *
     * @param string $siteName
     * @param string $url
     * @param string $group
     *
     * @throws AnalyticsServiceException
     *
     * @return int
     */
    public function registerSite(string $siteName, string $url, string $group);

    /**
     * Updated an existing site in the Analytics Service.
     *
     * @param string $idSite
     * @param string $siteName
     * @param string $url
     * @param string $group
     *
     * @throws AnalyticsServiceException
     *
     * @return int
     */
    public function updateSite(string $idSite, string $siteName, string $url, string $group);

    /**
     * Get Javascript code snippet for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite
     *
     * @throws AnalyticsServiceException
     *
     * @return string
     */
    public function getJavascriptSnippet(string $idSite);

    /**
     * Delete a given site in the Analytics Service.
     *
     * @param string $idSite
     *
     * @throws AnalyticsServiceException
     *
     * @return void
     */
    public function deleteSite(string $idSite);

    /**
     * Register a new user in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $password
     * @param string $email
     *
     * @throws AnalyticsServiceException
     *
     * @return string
     */
    public function registerUser(string $userLogin, string $password, string $email);

    /**
     * Get a specified user in the Analytics Service.
     *
     * @param string $email
     *
     * @throws AnalyticsServiceException
     *
     * @return string
     */
    public function getUserByEmail(string $email);

    /**
     * Delete a specified user in the Analytics Service.
     *
     * @param string $email
     *
     * @throws AnalyticsServiceException
     *
     * @return string
     */
    public function deleteUser(string $email);

    /**
     * Login and redirect a specified user in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $password
     *
     * @return void
     */
    public function loginAndRedirectUser(string $userLogin, string $password);

    /**
     * Set permissions for a specified user and specified websites
     * in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $access
     * @param string $idSites
     *
     * @throws AnalyticsServiceException
     *
     * @return string
     */
    public function setWebsitesAccess(string $userLogin, string $access, string $idSites);

    /**
     * Get total number of visits for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite
     * @param string $from
     *
     * @throws AnalyticsServiceException
     *
     * @return int
     */
    public function getSiteTotalVisits(string $idSite, string $from);

    /**
     * Get the number of visits for a specified site
     * registered last month in the Analytics Service.
     *
     * @param string $idSite
     *
     * @throws AnalyticsServiceException
     *
     * @return int
     */
    public function getSiteLastMonthVisits(string $idSite);
}
