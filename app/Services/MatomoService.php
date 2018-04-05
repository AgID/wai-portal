<?php

namespace App\Services;

use App\Contracts\AnalyticsService as AnalyticsServiceContract;
use App\Exceptions\AnalyticsServiceException;
use GuzzleHttp\Client as APIClient;
use GuzzleHttp\Exception\GuzzleException;

class MatomoService implements AnalyticsServiceContract
{
    protected $tokenAuth;
    protected $serviceBaseUri;
    protected $servicePublicUrl;
    protected $SSLVerify;

    /**
     * Create a new MatomoService instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tokenAuth = config('analytics-service.admin_token');
        $this->serviceBaseUri = config('analytics-service.api_base_uri');
        $this->servicePublicUrl = config('analytics-service.public_url');
        $this->SSLVerify = config('analytics-service.ssl_verify');
    }

    /**
     * Register a new site in the Analytics Service.
     *
     * @param  string $siteName
     * @param  string $url
     * @param  string $group
     * @return int
     * @throws AnalyticsServiceException
     */
    public function registerSite(string $siteName, string $url, string $group)
    {
        $params = [
            'method' => 'SitesManager.addSite',
            'siteName' => $siteName,
            'urls' => $url,
            'group' => $group,
            'timezone' => 'Europe/Rome',
            'currency' => 'EUR',
            //'excludeUnknownUrls' => true //TODO: enable in production!
        ];
        return $this->apiCall($params)['value'];
    }

    /**
     * Updated an existing site in the Analytics Service.
     *
     * @param  string $idSite
     * @param  string $siteName
     * @param  string $url
     * @param  string $group
     * @return int
     * @throws AnalyticsServiceException
     */
    public function updateSite(string $idSite, string $siteName, string $url, string $group)
    {
        $params = [
            'method' => 'SitesManager.updateSite',
            'idSite' => $idSite,
            'siteName' => $siteName,
            'urls' => $url
        ];
        return $this->apiCall($params);
    }

    /**
     * Get Javascript code snippet for a specified site
     * registered in the Analytics Service.
     *
     * @param  string $idSite
     * @return string
     * @throws AnalyticsServiceException
     */
    public function getJavascriptSnippet(string $idSite)
    {
        $params = [
            'method' => 'SitesManager.getJavascriptTag',
            'idSite' => $idSite
        ];
        return $this->apiCall($params)['value'];
    }

    /**
     * Delete a given site in the Analytics Service.
     *
     * @param  string $idSite
     * @return void
     * @throws AnalyticsServiceException
     */
    public function deleteSite(string $idSite)
    {
        $params = [
            'method' => 'SitesManager.deleteSite',
            'idSite' => $idSite
        ];
        return $this->apiCall($params);
    }

    /**
     * Register a new user in the Analytics Service.
     *
     * @param  string $userLogin
     * @param  string $password
     * @param  string $email
     * @return string
     * @throws AnalyticsServiceException
     */
    public function registerUser(string $userLogin, string $password, string $email)
    {
        $params = [
            'method' => 'UsersManager.addUser',
            'userLogin' => $userLogin,
            'password' => $password,
            'email' => $email
        ];
        return $this->apiCall($params);
    }

    /**
     * Get a specified user in the Analytics Service.
     *
     * @param  string $email
     * @return string
     * @throws AnalyticsServiceException
     */
    public function getUserByEmail(string $email)
    {
        $params = [
            'method' => 'UsersManager.getUserByEmail',
            'userEmail' => $email
        ];
        return $this->apiCall($params);
    }

    /**
     * Delete a specified user in the Analytics Service.
     *
     * @param  string $email
     * @return string
     * @throws AnalyticsServiceException
     */
    public function deleteUser(string $email)
    {
        $params = [
            'method' => 'UsersManager.deleteUser',
            'userLogin' => $email
        ];
        return $this->apiCall($params);
    }

    /**
     * Login and redirect a specified user in the Analytics Service.
     *
     * @param  string $userLogin
     * @param  string $password
     * @return void
     */
    public function loginAndRedirectUser(string $userLogin, string $password)
    {
        return redirect($this->servicePublicUrl.'/index.php?module=Login&action=logme&login='.$userLogin.'&password='.md5($password));
    }

    /**
     * Set permissions for a specified user and specified websites
     * in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $access
     * @param string $idSites
     * @return string
     * @throws AnalyticsServiceException
     */
    public function setWebsitesAccess(string $userLogin, string $access, string $idSites)
    {
        $params = [
            'method' => 'UsersManager.setUserAccess',
            'userLogin' => $userLogin,
            'access' => $access,
            'idSites' => $idSites

        ];
        return $this->apiCall($params);
    }

    /**
     * Get total number of visits for a specified site
     * registered in the Analytics Service.
     *
     * @param  string $idSite
     * @param  string $from
     * @return int
     * @throws AnalyticsServiceException
     */
    public function getSiteTotalVisits(string $idSite, string $from)
    {
        $params = [
            'method' => 'VisitsSummary.get',
            'idSite' => $idSite,
            'period' => 'range',
            'date' => $from . ',' . now()->format('Y-m-d')
        ];
        $response = $this->apiCall($params);
        if (isset($response['nb_visits'])) {
            return $response['nb_visits'];
        } else {
            return 0;
        }
    }

    /**
     * Get the number of visits for a specified site
     * registered last month in the Analytics Service.
     *
     * @param  string $idSite
     * @return int
     * @throws AnalyticsServiceException
     */
    public function getSiteLastMonthVisits(string $idSite)
    {
        $params = [
            'method' => 'VisitsSummary.get',
            'idSite' => $idSite,
            'period' => 'month',
            'date' => 'yesterday'
        ];
        $response = $this->apiCall($params);
        if (isset($response['nb_visits'])) {
            return $response['nb_visits'];
        } else {
            return 0;
        }
    }

    /**
     * Make an API call to Analytics Service.
     *
     * @param  array $params
     * @return string
     * @throws AnalyticsServiceException
     */
    protected function apiCall(array $params)
    {
        try {
            $client = new APIClient(['base_uri' => $this->serviceBaseUri]);
            $res = $client->request('GET', 'index.php', [
                'query' => array_merge($params, [
                    'module' => 'API',
                    'format' => 'JSON',
                    'token_auth' => $this->tokenAuth
                ]),
                'verify' => $this->SSLVerify
            ]);
        } catch (GuzzleException $exception) {
            throw new AnalyticsServiceException($exception->getMessage());
        }
        return json_decode($res->getBody(), true);
    }
}
