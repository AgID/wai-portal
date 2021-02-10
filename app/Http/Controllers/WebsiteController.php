<?php

namespace App\Http\Controllers;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserPermission;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Events\Website\WebsiteActivated;
use App\Events\Website\WebsiteAdded;
use App\Events\Website\WebsiteArchived;
use App\Events\Website\WebsiteUnarchived;
use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use App\Exceptions\InvalidWebsiteStatusException;
use App\Exceptions\OperationNotAllowedException;
use App\Exceptions\TenantIdNotSetException;
use App\Http\Requests\StorePrimaryWebsiteRequest;
use App\Http\Requests\StoreWebsiteRequest;
use App\Http\Requests\UpdateWebsiteRequest;
use App\Models\PublicAdministration;
use App\Models\Website;
use App\Traits\ActivatesWebsite;
use App\Traits\HasRoleAwareUrls;
use App\Traits\ManagePublicAdministrationRegistration;
use App\Traits\SendsResponse;
use App\Transformers\UsersPermissionsTransformer;
use App\Transformers\WebsiteApiTransformer;
use App\Transformers\WebsiteTransformer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

/**
 * Website management controller.
 */
class WebsiteController extends Controller
{
    use ActivatesWebsite;
    use SendsResponse;
    use HasRoleAwareUrls;
    use ManagePublicAdministrationRegistration;

    protected $redisCache;

    public function __construct()
    {
        $this->redisCache = Redis::connection(env('CACHE_CONNECTION'))->client();
    }

    /**
     * Display the websites list.
     *
     * @param PublicAdministration $publicAdministration the public administration the websites belong to
     *
     * @return View the view
     */
    public function index(PublicAdministration $publicAdministration): View
    {
        $websitesDatatable = [
            'datatableOptions' => [
                'searching' => [
                    'label' => __('cerca tra i siti web'),
                ],
                'columnFilters' => [
                    'type' => [
                        'filterLabel' => __('tipologia'),
                    ],
                    'status' => [
                        'filterLabel' => __('stato'),
                    ],
                ],
            ],
            'columns' => [
                ['data' => 'website_name', 'name' => __('nome del sito'), 'className' => 'text-wrap'],
                ['data' => 'type', 'name' => __('tipologia')],
                ['data' => 'added_at', 'name' => __('aggiunto il')],
                ['data' => 'status', 'name' => __('stato')],
                ['data' => 'icons', 'name' => '', 'orderable' => false],
                ['data' => 'buttons', 'name' => '', 'orderable' => false],
            ],
            'source' => $this->getRoleAwareUrl('websites.data.json', [], $publicAdministration),
            'caption' => __('elenco dei siti web presenti su :app', ['app' => config('app.name')]),
            'columnsOrder' => [['added_at', 'asc'], ['website_name', 'asc']],
        ];

        $websiteCreateUrl = $this->getRoleAwareUrl('websites.create', [], $publicAdministration);

        return view('pages.websites.index')->with(compact('websiteCreateUrl'))->with($websitesDatatable);
    }

    /**
     * Show the form for creating a new custom website.
     *
     * @return View the view
     */
    public function custom()
    {
        return view('pages.pa.add')->with('customForm', true);
    }

    /**
     * Create a new primary website.
     *
     * @param StorePrimaryWebsiteRequest $request the request
     *
     * @return RedirectResponse the server redirect response
     */
    public function storePrimary(StorePrimaryWebsiteRequest $request): RedirectResponse
    {
        $authUser = $request->user();

        $publicAdministration = PublicAdministration::make([
            'ipa_code' => $request->isCustomPublicAdministration ? Str::slug($request->publicAdministration['url']) : $request->publicAdministration['ipa_code'],
            'name' => $request->publicAdministration['name'],
            'pec' => $request->publicAdministration['pec'] ?? null,
            'rtd_name' => $request->publicAdministration['rtd_name'] ?? null,
            'rtd_mail' => $request->publicAdministration['rtd_mail'] ?? null,
            'rtd_pec' => $request->publicAdministration['rtd_pec'] ?? null,
            'city' => $request->publicAdministration['city'] ?? null,
            'county' => $request->publicAdministration['county'] ?? null,
            'region' => $request->publicAdministration['region'] ?? null,
            'type' => $request->publicAdministration['type'],
            'status' => PublicAdministrationStatus::PENDING,
        ]);

        $siteUrl = $request->isCustomPublicAdministration ? $request->publicAdministration['url'] : $request->publicAdministration['site'];
        $website = $this->registerPublicAdministration($authUser, $publicAdministration, $siteUrl, $request->isCustomPublicAdministration, $request->input('email'));

        event(new WebsiteAdded($website, $authUser));

        return redirect()->route('websites.index')->withModal([
            'title' => __('Il sito è stato inserito, adesso procedi ad attivarlo!'),
            'icon' => 'it-check-circle',
            'message' => __('Abbiamo inviato al tuo indirizzo email le istruzioni per attivare il sito e iniziare a monitorare il traffico.'),
            'image' => asset('images/primary-website-added.svg'),
        ]);
    }

    /**
     * Show the form for creating a new website.
     *
     * @param PublicAdministration $publicAdministration the public administration the new website will belong to
     *
     * @return View the view
     */
    public function create(PublicAdministration $publicAdministration): View
    {
        $usersPermissionsDatatableSource = $this->getRoleAwareUrl('websites.users.permissions.data.json', [
            'website' => null,
            'oldPermissions' => old('permissions'),
        ], $publicAdministration);
        $websiteStoreUrl = $this->getRoleAwareUrl('websites.store', [], $publicAdministration);
        $usersPermissionsDatatable = $this->getDatatableUsersPermissionsParams($usersPermissionsDatatableSource);

        return view('pages.websites.add')->with(compact('websiteStoreUrl'))->with($usersPermissionsDatatable);
    }

    public function store(StoreWebsiteRequest $request, PublicAdministration $publicAdministration): RedirectResponse
    {
        $this->storeMethod($request, $publicAdministration);

        $redirectUrl = $this->getRoleAwareUrl('websites.index', [], $publicAdministration);

        return redirect()->to($redirectUrl)->withModal([
            'title' => __('Il sito è stato inserito, adesso procedi ad attivarlo!'),
            'icon' => 'it-check-circle',
            'message' => __('Abbiamo inviato al tuo indirizzo email le istruzioni per attivare il sito e iniziare a monitorare il traffico.'),
            'image' => asset('images/website-added.svg'),
        ]);
    }

    public function storeApi(StoreWebsiteRequest $request): JsonResponse
    {
        $publicAdministration = get_public_administration_from_token();

        $response = $this->storeMethod($request, $publicAdministration);

        if (is_array($response) && array_key_exists('website', $response)) {
            $website = (new WebsiteApiTransformer())->transform($response['website']);

            return response()
                ->json($website, 201)
                ->header('Location', $this->getUriWebsiteAPI($response['website']));
        }

        return response()->json([
            'Error' => $response,
            'Message' => 'Il sito non è stato aggiunto',
        ], 400);
    }

    /**
     * Show the website details page.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to show
     *
     * @return View the view
     */
    public function show(PublicAdministration $publicAdministration, Website $website): View
    {
        $currentPublicAdministration = auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $usersPermissionsDatatableSourceUrl = $this->getRoleAwareUrl('websites.users.permissions.data.json', [
            'website' => $website,
        ], $currentPublicAdministration);
        $roleAwareUrls = $this->getRoleAwareUrlArray([
            'websiteEditUrl' => 'websites.edit',
            'websiteTrackingCheckUrl' => 'websites.tracking.check',
            'websiteTrackingForceUrl' => 'websites.activate.force',
            'websiteArchiveUrl' => 'websites.archive',
            'websiteUnarchiveUrl' => 'websites.unarchive',
            'javascriptSnippetUrl' => 'websites.snippet.javascript',
        ], [
            'website' => $website,
        ], $currentPublicAdministration);
        $usersPermissionsDatatable = $this->getDatatableUsersPermissionsParams($usersPermissionsDatatableSourceUrl, true);

        $authUser = auth()->user();
        $publicAdministrationUser = $authUser->publicAdministrations()->where('public_administration_id', $currentPublicAdministration->id)->first();
        if ($publicAdministrationUser) {
            $userPublicAdministrationStatus = UserStatus::fromValue(intval($publicAdministrationUser->pivot->user_status));
        }
        $forceActivationButtonVisible = !app()->environment('production') && config('wai.custom_public_administrations', false) && $website->type->is(WebsiteType::INSTITUTIONAL_PLAY);

        return view('pages.websites.show')->with(compact('website'))->with($roleAwareUrls)
            ->with($usersPermissionsDatatable)
            ->with('forceActivationButtonVisible', $forceActivationButtonVisible)
            ->with('userPublicAdministrationStatus', $userPublicAdministrationStatus ?? null);
    }

    public function showApi(Website $website)
    {
        $data = (new WebsiteApiTransformer())->transform($website);

        return response()->json($data, 200);
    }

    /**
     * Show the form for editing a website.
     *
     * @param Request $request the incoming request
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to edit
     *
     * @return View the view
     */
    public function edit(Request $request, PublicAdministration $publicAdministration, Website $website): View
    {
        $oldPermissions = old('permissions', $request->session()->hasOldInput() ? [] : null);
        $usersPermissionsDatatableSourceUrl = $this->getRoleAwareUrl('websites.users.permissions.data.json', [
            'website' => $website,
            'oldPermissions' => $oldPermissions,
        ], $publicAdministration);
        $updateUrl = $this->getRoleAwareUrl('websites.update', [
            'website' => $website,
        ], $publicAdministration);
        $usersPermissionsDatatable = $this->getDatatableUsersPermissionsParams($usersPermissionsDatatableSourceUrl);

        return view('pages.websites.edit')->with(compact('website', 'updateUrl'))->with($usersPermissionsDatatable);
    }

    /**
     * Update a website.
     * NOTE: for primary websites, only updates to user permissions are allowed.
     *
     * @param UpdateWebsiteRequest $request the request
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to update
     *
     * @return RedirectResponse the server redirect response
     */
    public function update(UpdateWebsiteRequest $request, PublicAdministration $publicAdministration, Website $website): RedirectResponse
    {
        $this->updateMethod($request, $publicAdministration, $website);

        $redirectUrl = $this->getRoleAwareUrl('websites.index', [], $publicAdministration);

        return redirect()->to($redirectUrl)->withNotification([
            'title' => __('modifica sito web'),
            'message' => __('La modifica del sito è andata a buon fine.'),
            'status' => 'success',
            'icon' => 'it-check-circle',
        ]);
    }

    public function updateApi(UpdateWebsiteRequest $request, Website $website)
    {
        $publicAdministration = get_public_administration_from_token();

        $response = $this->updateMethod($request, $publicAdministration, $website);
        $data = (new WebsiteApiTransformer())->transform($response);

        return response()->json($data, 200);
    }

    /**
     * Delete a website.
     * NOTE: super-admin only.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to delete
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function delete(PublicAdministration $publicAdministration, Website $website)
    {
        if ($website->trashed()) {
            return $this->notModifiedResponse();
        }

        try {
            if ($website->type->is(WebsiteType::INSTITUTIONAL)) {
                throw new OperationNotAllowedException('Delete request not allowed on primary website ' . $website->info . '.');
            }

            app()->make('analytics-service')->changeArchiveStatus($website->analytics_id, WebsiteStatus::ARCHIVED);
            $website->delete();

            $this->updateWebsiteListCache($website);

            return $this->websiteResponse($website);
        } catch (AnalyticsServiceException | BindingResolutionException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Internal Server Error';
            $httpStatusCode = 500;
        } catch (OperationNotAllowedException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = $exception->getMessage();
            $httpStatusCode = 400;
        } catch (CommandErrorException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Bad Request';
            $httpStatusCode = 400;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    /**
     * Restore a deleted website.
     * NOTE: super-admin only.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to restore
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function restore(PublicAdministration $publicAdministration, Website $website)
    {
        if (!$website->trashed()) {
            return $this->notModifiedResponse();
        }

        try {
            // NOTE: WebsiteStatus::ACTIVE is for re-enabling tracking on the Analytics Service: actual website status isn't changed
            app()->make('analytics-service')->changeArchiveStatus($website->analytics_id, WebsiteStatus::ACTIVE);
            $website->restore();

            return $this->websiteResponse($website);
        } catch (AnalyticsServiceException | BindingResolutionException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Internal Server Error';
            $httpStatusCode = 500;
        } catch (CommandErrorException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Bad Request';
            $httpStatusCode = 400;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    /**
     * Check website tracking status.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to check
     *
     * @return JsonResponse|RedirectResponse the response
     */
    public function checkTracking(PublicAdministration $publicAdministration, Website $website)
    {
        try {
            if ($website->status->is(WebsiteStatus::PENDING)) {
                if ($this->hasActivated($website)) {
                    $this->activate($website);

                    event(new WebsiteActivated($website));

                    return $this->websiteResponse($website);
                }

                return $this->notModifiedResponse();
            }

            throw new InvalidWebsiteStatusException('Unable to check activation for website ' . $website->info . ' in status ' . $website->status->key . '.');
        } catch (AnalyticsServiceException | BindingResolutionException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Internal Server Error';
            $httpStatusCode = 500;
        } catch (InvalidWebsiteStatusException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current website status';
            $httpStatusCode = 400;
        } catch (CommandErrorException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Bad Request';
            $httpStatusCode = 400;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    /**
     * Force website tracking status to active.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to check
     *
     * @return JsonResponse|RedirectResponse the response
     */
    public function forceActivation(PublicAdministration $publicAdministration, Website $website)
    {
        try {
            if ($website->status->is(WebsiteStatus::PENDING) && !app()->environment('production')) {
                $this->activate($website);
                event(new WebsiteActivated($website));

                return $this->websiteResponse($website);
            }

            throw new InvalidWebsiteStatusException('Unable to force activation for website ' . $website->info . ' in status ' . $website->status->key . '.');
        } catch (AnalyticsServiceException | BindingResolutionException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Internal Server Error';
            $httpStatusCode = 500;
        } catch (InvalidWebsiteStatusException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current website status';
            $httpStatusCode = 400;
        } catch (CommandErrorException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Bad Request';
            $httpStatusCode = 400;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    public function forceActivationApi(Website $website)
    {
        $publicAdministration = get_public_administration_from_token();

        return $this->forceActivation($publicAdministration, $website);
    }

    public function archiveApi(Website $website)
    {
        $publicAdministration = get_public_administration_from_token();

        return $this->archive($publicAdministration, $website);
    }

    /**
     * Archive website request.
     * Only active and not primary type websites can be archived.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website
     *
     * @return JsonResponse|RedirectResponse the response
     */
    public function archive(PublicAdministration $publicAdministration, Website $website)
    {
        if ($website->status->is(WebsiteStatus::ARCHIVED)) {
            return $this->notModifiedResponse();
        }

        try {
            if (!$website->type->is(WebsiteType::INSTITUTIONAL)) {
                if ($website->status->is(WebsiteStatus::ACTIVE)) {
                    $website->status = WebsiteStatus::ARCHIVED;
                    app()->make('analytics-service')->changeArchiveStatus($website->analytics_id, WebsiteStatus::ARCHIVED);
                    $website->save();

                    event(new WebsiteArchived($website, true));

                    return $this->websiteResponse($website);
                }

                throw new InvalidWebsiteStatusException('Unable to archive website ' . $website->info . ' in status ' . $website->status->key . '.');
            }

            throw new OperationNotAllowedException('Archive request not allowed on primary website ' . $website->info . '.');
        } catch (AnalyticsServiceException | BindingResolutionException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Internal Server Error';
            $httpStatusCode = 500;
        } catch (InvalidWebsiteStatusException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current website status';
            $httpStatusCode = 400;
        } catch (OperationNotAllowedException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current website';
            $httpStatusCode = 400;
        } catch (CommandErrorException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Bad Request';
            $httpStatusCode = 400;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    /**
     * Re-enable an archived website.
     * Only archived and not primary type websites can be re-enabled.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website
     *
     * @return JsonResponse|RedirectResponse the response
     */
    public function unarchive(PublicAdministration $publicAdministration, Website $website)
    {
        try {
            if (!$website->type->is(WebsiteType::INSTITUTIONAL)) {
                if ($website->status->is(WebsiteStatus::ACTIVE)) {
                    return $this->notModifiedResponse();
                }

                if ($website->status->is(WebsiteStatus::ARCHIVED)) {
                    $website->status = WebsiteStatus::ACTIVE;
                    app()->make('analytics-service')->changeArchiveStatus($website->analytics_id, WebsiteStatus::ACTIVE);
                    $website->save();

                    event(new WebsiteUnarchived($website));

                    return $this->websiteResponse($website);
                }

                throw new InvalidWebsiteStatusException('Unable to cancel archiving for website ' . $website->info . ' in status ' . $website->status->key . '.');
            }

            throw new OperationNotAllowedException('Cancel archiving request not allowed on primary website ' . $website->info . '.');
        } catch (AnalyticsServiceException | BindingResolutionException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Internal Server Error';
            $httpStatusCode = 500;
        } catch (InvalidWebsiteStatusException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current website status';
            $httpStatusCode = 400;
        } catch (OperationNotAllowedException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current website';
            $httpStatusCode = 400;
        } catch (CommandErrorException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Bad Request';
            $httpStatusCode = 400;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    public function unarchiveApi(Website $website)
    {
        $publicAdministration = get_public_administration_from_token();

        return $this->unarchive($publicAdministration, $website);
    }

    /**
     * Get Javascript snippet for a website.
     *
     * @param Website $website the website
     *
     * @throws BindingResolutionException if unable to bind to the service
     * @throws AnalyticsServiceException if unable to contact the Analytics Service
     * @throws CommandErrorException if command finishes with error
     *
     * @return JsonResponse the JSON response
     */
    public function showJavascriptSnippet(PublicAdministration $publicAdministration, Website $website): JsonResponse
    {
        try {
            $javascriptSnippet = app()->make('analytics-service')->getJavascriptSnippet($website->analytics_id);

            return response()->json([
                'result' => 'ok',
                'id' => $website->slug,
                'name' => e($website->name),
                'javascriptSnippet' => trim($javascriptSnippet),
            ]);
        } catch (AnalyticsServiceException | BindingResolutionException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Internal Server Error';
            $httpStatusCode = 500;
        } catch (CommandErrorException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Bad Request';
            $httpStatusCode = 400;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    /**
     * Get the websites data.
     *
     * @param PublicAdministration $publicAdministration the Public Administration to filter websites or null to use current one
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response in JSON format
     */
    public function dataJson(PublicAdministration $publicAdministration)
    {
        return DataTables::of($this->baseDataJson($publicAdministration))
            ->setTransformer(new WebsiteTransformer())
            ->make(true);
    }

    public function dataApi(Request $request): JsonResponse
    {
        $publicAdministration = get_public_administration_from_token();
        $websites = $publicAdministration->websites()->get()->map(function ($website) {
            return (new WebsiteApiTransformer())->transform($website);
        });

        return response()->json($websites, 200);
    }

    public function websiteList(Request $request): JsonResponse
    {
        $publicAdministration = PublicAdministration::find($request->id);
        $websites = $publicAdministration->websites()->get()->toArray();
        $websites = array_map(function ($elem) {
            return $elem['url'];
        }, $websites);

        return response()->json($websites, 200);
    }

    /**
     * Get the user permissions on a website.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to use for permissions initialization
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response in JSON format
     */
    public function dataUsersPermissionsJson(PublicAdministration $publicAdministration, Website $website)
    {
        $users = auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration->users
            : current_public_administration()->users()->where('status', '!=', UserStatus::SUSPENDED);

        return DataTables::of($users)
            ->setTransformer(new UsersPermissionsTransformer())
            ->make(true);
    }

    /**
     * Get the datatable parameters for users permission with specified source.
     *
     * @param string $source the source paramater for the users permission datatable
     * @param bool $readonly wether the datatable is readonly
     *
     * @return array the datatable parameters
     */
    public function getDatatableUsersPermissionsParams(string $source, bool $readonly = false): array
    {
        return [
            'datatableOptions' => [
                'searching' => [
                    'label' => __('cerca tra gli utenti'),
                ],
            ],
            'columns' => [
                ['data' => 'name', 'name' => __('nome e cognome')],
                ['data' => 'email', 'name' => __('email')],
                ['data' => 'status', 'name' => __('stato')],
                ['data' => ($readonly ? 'icons' : 'toggles'), 'name' => __('permessi sui dati analytics'), 'orderable' => false, 'searchable' => false],
            ],
            'source' => $source . ($readonly ? '?readOnly' : ''),
            'caption' => __('elenco degli utenti presenti su :app', ['app' => config('app.name')]),
            'columnsOrder' => [['name', 'asc']],
        ];
    }

    /**
     * Store a new website.
     *
     * @param StoreWebsiteRequest $request the request
     * @param PublicAdministration $publicAdministration the public administration the website will belong to
     *
     * @return array the new website
     */
    protected function storeMethod(StoreWebsiteRequest $request, PublicAdministration $publicAdministration): array
    {
        $validatedData = $request->validated();
        $user = auth()->user();
        if (null !== $user) {
            $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
                ? $publicAdministration
                : current_public_administration();
        } else {
            $currentPublicAdministration = get_public_administration_from_token();
        }

        $analyticsId = app()->make('analytics-service')->registerSite($validatedData['website_name'], $validatedData['url'], $currentPublicAdministration->name);

        $website = Website::create([
            'name' => $validatedData['website_name'],
            'url' => $validatedData['url'],
            'type' => (int) $validatedData['type'],
            'public_administration_id' => $currentPublicAdministration->id,
            'analytics_id' => $analyticsId,
            'slug' => Str::slug($validatedData['url']),
            'status' => WebsiteStatus::PENDING,
        ]);

        if (null !== $user) {
            event(new WebsiteAdded($website, $user));
        }

        $currentPublicAdministration->getAdministrators()->map(function ($administrator) use ($website, $currentPublicAdministration) {
            $administrator->setWriteAccessForWebsite($website);
            $administrator->syncWebsitesPermissionsToAnalyticsService($currentPublicAdministration);
        });

        $this->manageWebsitePermissionsOnNonAdministrators($validatedData, $currentPublicAdministration, $website);

        $this->updateWebsiteListCache($website);

        return [
            'website' => $website,
        ];
    }

    protected function updateMethod(UpdateWebsiteRequest $request, PublicAdministration $publicAdministration, Website $website)
    {
        $validatedData = $request->validated();

        $user = auth()->user();
        if (null !== $user) {
            $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
                ? $publicAdministration
                : current_public_administration();
        } elseif (null !== $request->get('publicAdministration')) {
            $currentPublicAdministration = get_public_administration_from_token();
        }

        if (!$website->type->is(WebsiteType::INSTITUTIONAL)) {
            if ($website->slug !== Str::slug($validatedData['url'])) {
                app()->make('analytics-service')->updateSite($website->analytics_id, $validatedData['website_name'] . ' [' . $validatedData['type'] . ']', $validatedData['url'], $website->publicAdministration->name);
            }

            $website->fill([
                'name' => $validatedData['website_name'],
                'url' => $validatedData['url'],
                'type' => $validatedData['type'],
                'slug' => Str::slug($validatedData['url']),
            ]);
            $website->save();
        }

        $this->manageWebsitePermissionsOnNonAdministrators($validatedData, $currentPublicAdministration, $website);

        $this->updateWebsiteListCache($website);

        return $website;
    }

    protected function baseDataJson(PublicAdministration $publicAdministration)
    {
        $publicAdministrationHelper = current_public_administration();

        $data = auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration->websites()->withTrashed()->get()
            : $publicAdministrationHelper->websites();

        return $data;
    }

    /**
     * Manage non-admin users permissions on a website.
     *
     * @param array $validatedData the permissions array
     * @param PublicAdministration $publicAdministration the public administration the website belogns to
     * @param Website $website the website
     *
     * @throws BindingResolutionException if unable to bind to the service
     * @throws AnalyticsServiceException if unable to contact the Analytics Service
     * @throws CommandErrorException if command finishes with error
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     */
    private function manageWebsitePermissionsOnNonAdministrators(array $validatedData, PublicAdministration $publicAdministration, Website $website): void
    {
        $usersPermissions = $validatedData['permissions'] ?? [];
        $publicAdministration->getNonAdministrators()->map(function ($user) use ($website, $usersPermissions) {
            if (empty($usersPermissions[$user->id])) {
                $user->setNoAccessForWebsite($website);

                return $user;
            }

            if (in_array(UserPermission::MANAGE_ANALYTICS, $usersPermissions[$user->id])) {
                $user->setWriteAccessForWebsite($website);

                return $user;
            }

            if (in_array(UserPermission::READ_ANALYTICS, $usersPermissions[$user->id])) {
                $user->setViewAccessForWebsite($website);

                return $user;
            }
        })->map(function ($user) use ($publicAdministration) {
            if ($user->hasAnalyticsServiceAccount()) {
                $user->syncWebsitesPermissionsToAnalyticsService($publicAdministration);
            }
        });
    }

    private function updateWebsiteListCache(Website $website)
    {
        $id = $website->analytics_id;
        $list = app()->make('analytics-service')->getSiteUrlsFromId($id);

        $this->redisCache->set($id, implode(' ', $list));
    }

    private function getUriWebsiteAPI(Website $website): string
    {
        return config('kong-service.api_url') .
            str_replace('/api/', '/portal/', route('api.sites.read', ['website' => $website], false));
    }
}
