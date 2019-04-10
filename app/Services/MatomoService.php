<?php

namespace App\Services;

use App\Contracts\AnalyticsService as AnalyticsServiceContract;
use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteStatus;
use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use GuzzleHttp\Client as APIClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;

/**
 * Matomo implementation of Analytics Service.
 */
class MatomoService implements AnalyticsServiceContract
{
    /**
     * Map application website access levels into Matomo Service ones.
     *
     * @var array the mappings
     */
    private const ACCESS_LEVELS_MAPPINGS = [
        WebsiteAccessType::NO_ACCESS => 'noaccess',
        WebsiteAccessType::VIEW => 'view',
        WebsiteAccessType::WRITE => 'write',
        WebsiteAccessType::ADMIN => 'admin',
    ];

    /**
     * @var array
     */
    private const ARCHIVE_STATUS_MAPPINGS = [
        WebsiteStatus::ACTIVE => 'off',
        WebsiteStatus::ARCHIVED => 'on',
    ];

    /**
     * Local service URL.
     *
     * @var string the local URL
     */
    protected $serviceBaseUri;

    /**
     * Public service URL.
     *
     * @var string the public URL
     */
    protected $servicePublicUrl;

    /**
     * SSL verification flag.
     *
     * @var bool true to check SSL certificates, false to skip
     */
    protected $SSLVerify;

    /**
     * Create a new Matomo Service instance.
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
     * @param string $siteName the website name
     * @param string $url the website URL
     * @param string $group the website group
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return int the Analytics Service website ID
     */
    public function registerSite(string $siteName, string $url, string $group): int
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
     * @param string $idSite the Analytics Service website ID
     * @param string $siteName the website name
     * @param string $url the website URL
     * @param string $group the website group
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function updateSite(string $idSite, string $siteName, string $url, string $group, string $tokenAuth): void
    {
        $params = [
            'method' => 'SitesManager.updateSite',
            'idSite' => $idSite,
            'siteName' => $siteName,
            'urls' => $url,
            'token_auth' => $tokenAuth,
        ];

        $this->apiCall($params);
    }

    /**
     * @param string $idSites
     * @param int $status
     * @param string $tokenAuth
     *
     * @throws AnalyticsServiceException
     * @throws CommandErrorException
     */
    public function changeArchiveStatus(string $idSites, int $status, string $tokenAuth): void
    {
        if (WebsiteStatus::ARCHIVED !== $status && (WebsiteStatus::ACTIVE !== $status)) {
            throw new CommandErrorException('Invalid parameter for archiving: must be ' . WebsiteStatus::ACTIVE . ' or ' . WebsiteStatus::ARCHIVED . '. Received: ' . $status);
        }

        $params = [
            'method' => 'DisableTracking.changeDisableState',
            'idSites' => $idSites,
            'token_auth' => $tokenAuth,
            'disable' => self::ARCHIVE_STATUS_MAPPINGS[$status],
        ];

        $this->apiCall($params);
    }

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
    public function getJavascriptSnippet(string $idSite, string $tokenAuth): string
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
     * @param string $idSite the Analytics Service website ID
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function deleteSite(string $idSite, string $tokenAuth): void
    {
        $params = [
            'method' => 'SitesManager.deleteSite',
            'idSite' => $idSite,
            'token_auth' => $tokenAuth,
        ];

        $this->apiCall($params);
    }

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
    public function registerUser(string $userLogin, string $password, string $email, string $tokenAuth): void
    {
        $params = [
            'method' => 'UsersManager.addUser',
            'userLogin' => $userLogin,
            'password' => $password,
            'email' => $email,
            'token_auth' => $tokenAuth,
        ];

        $this->apiCall($params);
    }

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
    public function getUserByEmail(string $email, string $tokenAuth): array
    {
        $params = [
            'method' => 'UsersManager.getUserByEmail',
            'userEmail' => $email,
            'token_auth' => $tokenAuth,
        ];

        return $this->apiCall($params);
    }

    /**
     * @param string $userLogin the Analytics Service user ID
     * @param string $hashedPassword the MD5 hashed Analytics Service user password
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return string the Analytics authentication token
     */
    public function getUserAuthToken(string $userLogin, string $hashedPassword): string
    {
        $params = [
            'method' => 'UsersManager.getTokenAuth',
            'userLogin' => $userLogin,
            'md5Password' => $hashedPassword,
        ];

        return $this->apiCall($params)['value'];
    }

    /**
     * Delete a specified user in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws CommandErrorException if command is unsuccessful
     */
    public function deleteUser(string $userLogin, string $tokenAuth): void
    {
        $params = [
            'method' => 'UsersManager.deleteUser',
            'userLogin' => $userLogin,
            'token_auth' => $tokenAuth,
        ];

        $this->apiCall($params);
    }

    /**
     * Login and redirect a specified user in the Analytics Service.
     *
     * @param string $userLogin the Analytics Service user ID
     * @param string $hashedPassword the MD5 hashed Analytics Service user password
     *
     * @return RedirectResponse the Analytics service dashboard
     */
    public function loginAndRedirectUser(string $userLogin, string $hashedPassword): RedirectResponse
    {
        return redirect($this->servicePublicUrl . '/index.php?module=Login&action=logme&login=' . $userLogin . '&password=' . $hashedPassword);
    }

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
    public function setWebsiteAccess(string $userLogin, int $access, string $idSites, string $tokenAuth): void
    {
        $params = [
            'method' => 'UsersManager.setUserAccess',
            'userLogin' => $userLogin,
            'access' => self::ACCESS_LEVELS_MAPPINGS[$access],
            'idSites' => $idSites,
            'token_auth' => $tokenAuth,
        ];

        $this->apiCall($params);
    }

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
    public function getLiveVisits(string $idSite, int $minutes, string $tokenAuth): int
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
     * @param string $idSite the Analytics Service website ID
     * @param string $from the date range
     * @param string $tokenAuth the Analytics authentication token
     *
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return int the total reported website visits
     */
    public function getSiteTotalVisitsFrom(string $idSite, string $from, string $tokenAuth): int
    {
        $params = [
            'method' => 'VisitsSummary.get',
            'idSite' => $idSite,
            'period' => 'range',
            'date' => $from . ',' . now()->format('Y-m-d'),
            'token_auth' => $tokenAuth,
        ];
        $response = $this->apiCall($params);

        return $response['nb_visits'] ?? 0;
    }

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
    public function getSiteLastMonthVisits(string $idSite, string $tokenAuth): int
    {
        $params = [
            'method' => 'VisitsSummary.get',
            'idSite' => $idSite,
            'period' => 'month',
            'date' => 'yesterday',
            'token_auth' => $tokenAuth,
        ];
        $response = $this->apiCall($params);

        return $response['nb_visits'] ?? 0;
    }

    /**
     * Make an API call to Analytics Service.
     *
     * @param array $params the request parameter
     *
     * @throws AnalyticsServiceException if unable to contact the Analytics Service
     * @throws CommandErrorException if command finishes with error status
     *
     * @return array the JSON response
     */
    protected function apiCall(array $params): array
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
