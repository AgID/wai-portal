<?php

namespace App\Contracts;

use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;

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
     * @throws CommandErrorException
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
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return int
     */
    public function updateSite(string $idSite, string $siteName, string $url, string $group, string $tokenAuth);

    /**
     * Get Javascript code snippet for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function getJavascriptSnippet(string $idSite, string $tokenAuth);

    /**
     * Delete a given site in the Analytics Service.
     *
     * @param string $idSite
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return void
     */
    public function deleteSite(string $idSite, string $tokenAuth);

    /**
     * Register a new user in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $password
     * @param string $email
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function registerUser(string $userLogin, string $password, string $email, string $tokenAuth, string $alias = '');

    /**
     * Get a specified user in the Analytics Service.
     *
     * @param string $email
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function getUserByEmail(string $email, string $tokenAuth);

    /**
     * @param string $userLogin
     * @param string $hashedPassword
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function getUserAuthToken(string $userLogin, string $hashedPassword);

    /**
     * Delete a specified user in the Analytics Service.
     *
     * @param string $email
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function deleteUser(string $email, string $tokenAuth);

    /**
     * Login and redirect a specified user in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $hashedPassword
     *
     * @return void
     */
    public function loginAndRedirectUser(string $userLogin, string $hashedPassword);

    /**
     * Set permissions for a specified user and specified websites
     * in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $access
     * @param string $idSites
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function setWebsitesAccess(string $userLogin, string $access, string $idSites, string $tokenAuth);

    /**
     * @param string $idSite
     * @param int $minutes
     * @param string $tokenAuth
     *
     * @return int
     */
    public function getLiveVisits(string $idSite, int $minutes, string $tokenAuth);

    /**
     * Get total number of visits for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite
     * @param string $from
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return int
     */
    public function getSiteTotalVisits(string $idSite, string $from, string $tokenAuth);

    /**
     * Get the number of visits for a specified site
     * registered last month in the Analytics Service.
     *
     * @param string $idSite
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return int
     */
    public function getSiteLastMonthVisits(string $idSite, string $tokenAuth);
}
