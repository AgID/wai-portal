<?php

namespace App\Contracts;

interface AnalyticsService
{
    /**
     * Register a new site in the Analytics Service.
     *
     * @param  string $siteName
     * @param  string $url
     * @param  string $group
     * @return int
     */
    public function registerSite(string $siteName, string $url, string $group);

    /**
     * Get Javascript code snippet for a specified site
     * registered in the Analytics Service.
     *
     * @param  string $idSite
     * @return string
     */
    public function getJavascriptSnippet(string $idSite);

    /**
     * Delete a given site in the Analytics Service.
     *
     * @param  string $idSite
     * @return void
     */
    public function deleteSite(string $idSite);

    /**
     * Register a new user in the Analytics Service.
     *
     * @param  string $userLogin
     * @param  string $password
     * @param  string $email
     * @return string
     */
    public function registerUser(string $userLogin, string $password, string $email);

    /**
     * Get a specified user in the Analytics Service.
     *
     * @param  string $email
     * @return string
     */
    public function getUserByEmail(string $email);

    /**
     * Delete a specified user in the Analytics Service.
     *
     * @param  string $email
     * @return string
     * @throws AnalyticsServiceException
     */
    public function deleteUser(string $email);

    /**
     * Set permissions for a specified user and specified websites
     * in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $access
     * @param string $idSites
     * @return string
     */
    public function setWebsitesAccess(string $userLogin, string $access, string $idSites);

    /**
     * Login and redirect a specified user in the Analytics Service.
     *
     * @param  string $userLogin
     * @param  string $password
     * @return void
     */
    public function loginAndRedirectUser(string $userLogin, string $password);
}
