<?php

namespace App\Services;

use App\Contracts\AnalyticsService as AnalyticsServiceContract;
use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use GuzzleHttp\Client as APIClient;
use GuzzleHttp\Exception\GuzzleException;

class MatomoService implements AnalyticsServiceContract
{
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
        $this->serviceBaseUri = config('analytics-service.api_base_uri');
        $this->servicePublicUrl = config('analytics-service.public_url');
        $this->SSLVerify = config('analytics-service.ssl_verify');
    }

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
    public function registerSite(string $siteName, string $url, string $group)
    {
        $params = [
            'method' => 'SitesManager.addSite',
            'siteName' => $siteName,
            'urls' => $url,
            'group' => $group,
            'timezone' => 'Europe/Rome',
            'currency' => 'EUR',
            'token_auth' => config('analytics-service.admin_token'),
            //'excludeUnknownUrls' => true //TODO: enable in production!
        ];

        return $this->apiCall($params)['value'];
    }

    /**
     * Updated an existing site in the Analytics Service.
     *
     * @param string $idSite
     * @param string $siteName
     * @param string $url
     * @param string $group
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return int
     */
    public function updateSite(string $idSite, string $siteName, string $url, string $group, string $tokenAuth)
    {
        $params = [
            'method' => 'SitesManager.updateSite',
            'idSite' => $idSite,
            'siteName' => $siteName,
            'urls' => $url,
            'token_auth' => $tokenAuth,
        ];

        return $this->apiCall($params);
    }

    /**
     * Get Javascript code snippet for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function getJavascriptSnippet(string $idSite, string $tokenAuth)
    {
        $params = [
            'method' => 'SitesManager.getJavascriptTag',
            'idSite' => $idSite,
            'token_auth' => $tokenAuth,
        ];

        return $this->apiCall($params)['value'];
    }

    /**
     * Delete a given site in the Analytics Service.
     *
     * @param string $idSite
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return void
     */
    public function deleteSite(string $idSite, string $tokenAuth)
    {
        $params = [
            'method' => 'SitesManager.deleteSite',
            'idSite' => $idSite,
            'token_auth' => $tokenAuth,
        ];

        return $this->apiCall($params);
    }

    /**
     * Register a new user in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $password
     * @param string $email
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function registerUser(string $userLogin, string $password, string $email, string $tokenAuth, string $alias = '')
    {
        $params = [
            'method' => 'UsersManager.addUser',
            'userLogin' => $userLogin,
            'password' => $password,
            'email' => $email,
            'alias' => $alias,
            'token_auth' => $tokenAuth,
        ];

        return $this->apiCall($params);
    }

    /**
     * @param string $userLogin
     * @param string $hashedPassword
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function getUserAuthToken(string $userLogin, string $hashedPassword)
    {
        $params = [
            'method' => 'UsersManager.getTokenAuth',
            'userLogin' => $userLogin,
            'md5Password' => $hashedPassword,
        ];

        return $this->apiCall($params)['value'];
    }

    /**
     * Get a specified user in the Analytics Service.
     *
     * @param string $email
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function getUserByEmail(string $email, string $tokenAuth)
    {
        $params = [
            'method' => 'UsersManager.getUserByEmail',
            'userEmail' => $email,
            'token_auth' => $tokenAuth,
        ];

        return $this->apiCall($params);
    }

    /**
     * Delete a specified user in the Analytics Service.
     *
     * @param string $userLogin
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function deleteUser(string $userLogin, string $tokenAuth)
    {
        $params = [
            'method' => 'UsersManager.deleteUser',
            'userLogin' => $userLogin,
            'token_auth' => $tokenAuth,
        ];

        return $this->apiCall($params);
    }

    /**
     * Login and redirect a specified user in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $password
     *
     * @return void
     */
    public function loginAndRedirectUser(string $userLogin, string $hashedPassword)
    {
        return redirect($this->servicePublicUrl . '/index.php?module=Login&action=logme&login=' . $userLogin . '&password=' . $hashedPassword);
    }

    /**
     * Set permissions for a specified user and specified websites
     * in the Analytics Service.
     *
     * @param string $userLogin
     * @param string $access
     * @param string $idSites
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    public function setWebsitesAccess(string $userLogin, string $access, string $idSites, string $tokenAuth)
    {
        $params = [
            'method' => 'UsersManager.setUserAccess',
            'userLogin' => $userLogin,
            'access' => $access,
            'idSites' => $idSites,
            'token_auth' => $tokenAuth,
        ];

        return $this->apiCall($params);
    }

    /**
     * @param string $idSite
     * @param int $minutes
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return int
     */
    public function getLiveVisits(string $idSite, int $minutes, string $tokenAuth)
    {
        $params = [
            'method' => 'Live.getCounters',
            'idSite' => $idSite,
            'lastMinutes' => $minutes,
            'token_auth' => $tokenAuth,
        ];
        $response = $this->apiCall($params);
        if (!empty($response)) {
            return $response[0]['visits'];
        }

        return 0;
    }

    /**
     * Get total number of visits for a specified site
     * registered in the Analytics Service.
     *
     * @param string $idSite
     * @param string $from
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return int
     */
    public function getSiteTotalVisits(string $idSite, string $from, string $tokenAuth)
    {
        $params = [
            'method' => 'VisitsSummary.get',
            'idSite' => $idSite,
            'period' => 'range',
            'date' => $from . ',' . now()->format('Y-m-d'),
            'token_auth' => $tokenAuth,
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
     * @param string $idSite
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return int
     */
    public function getSiteLastMonthVisits(string $idSite, string $tokenAuth)
    {
        $params = [
            'method' => 'VisitsSummary.get',
            'idSite' => $idSite,
            'period' => 'month',
            'date' => 'yesterday',
            'token_auth' => $tokenAuth,
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
     * @param array $params
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     *
     * @return string
     */
    protected function apiCall(array $params)
    {
        try {
            $client = new APIClient(['base_uri' => $this->serviceBaseUri]);
            $res = $client->request('GET', 'index.php', [
                'query' => array_merge($params, [
                    'module' => 'API',
                    'format' => 'JSON',
                ]),
                'verify' => $this->SSLVerify,
            ]);
        } catch (GuzzleException $exception) {
            throw new AnalyticsServiceException($exception->getMessage());
        }
        $response = json_decode($res->getBody(), true);
        if (!empty($response['result']) && 'error' === $response['result']) {
            throw new CommandErrorException($response['message']);
        }

        return $response;
    }
}
