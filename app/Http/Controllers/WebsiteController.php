<?php

namespace App\Http\Controllers;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Events\PublicAdministration\PublicAdministrationRegistered;
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
use App\Transformers\UsersPermissionsTransformer;
use App\Transformers\WebsiteTransformer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Yajra\Datatables\Datatables;

/**
 * Website management controller.
 */
class WebsiteController extends Controller
{
    use ActivatesWebsite;

    /**
     * Display website list.
     *
     * @return \Illuminate\View\View the view
     */
    public function index(): View
    {
        $websitesDatatable = [
            'columns' => [
                ['data' => 'name', 'name' => 'Nome'],
                ['data' => 'url', 'name' => 'URL'],
                ['data' => 'type', 'name' => 'Tipo'],
                ['data' => 'added_at', 'name' => 'Aggiunto il'],
                ['data' => 'status', 'name' => 'Stato'],
                ['data' => 'buttons', 'name' => 'Azioni'],
            ],
            'source' => request()->user()->can(UserPermission::ACCESS_ADMIN_AREA)
                ? route('admin.publicAdministration.websites.data.json', ['publicAdministration' => request()->route('publicAdministration')])
                : route('websites.data.json'),
            'caption' => 'Elenco dei siti web abilitati su Web Analytics Italia', //TODO: set title in lang file
            'footer' => '*Il numero di visite si riferisce agli ultimi 30 giorni.',
            'columnsOrder' => [['added_at', 'asc'], ['name', 'asc']],
        ];

        return view('pages.websites.index')->with($websitesDatatable);
    }

    /**
     * Show the form for creating a new primary website.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse the view or the server redirect
     *                                                                 if a primary website already exists
     */
    public function createPrimary()
    {
        if (auth()->user()->publicAdministrations->isNotEmpty()) {
            return redirect()->route('websites.index');
        }

        return view('pages.websites.add_primary');
    }

    /**
     * Create a new primary website.
     *
     * @param StorePrimaryWebsiteRequest $request the request
     *
     * @throws BindingResolutionException if unable to bind to the analytics service
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function storePrimary(StorePrimaryWebsiteRequest $request): RedirectResponse
    {
        $publicAdministration = PublicAdministration::make([
            'ipa_code' => $request->publicAdministration['ipa_code'],
            'name' => $request->publicAdministration['name'],
            'pec_address' => $request->publicAdministration['pec'] ?? null,
            'city' => $request->publicAdministration['city'],
            'county' => $request->publicAdministration['county'],
            'region' => $request->publicAdministration['region'],
            'type' => $request->publicAdministration['type'],
            'status' => PublicAdministrationStatus::PENDING,
        ]);

        $primaryWebsiteURL = $request->publicAdministration['site'];
        $analyticsId = app()->make('analytics-service')->registerSite('Sito istituzionale', $primaryWebsiteURL, $publicAdministration->name); //TODO: put string in lang file

        $publicAdministration->save();
        $website = Website::create([
            'name' => 'Sito istituzionale', //TODO: put in lang file
            'url' => $primaryWebsiteURL,
            'type' => WebsiteType::PRIMARY,
            'public_administration_id' => $publicAdministration->id,
            'analytics_id' => $analyticsId,
            'slug' => Str::slug($primaryWebsiteURL),
            'status' => WebsiteStatus::PENDING,
        ]);

        $publicAdministration->users()->save($request->user());
        // This is the first time we know which public administration the
        // current user belongs, so we need to set the tenant id just now.
        session()->put('tenant_id', $publicAdministration->id);
        $request->user()->roles()->detach();
        Bouncer::scope()->to($publicAdministration->id);
        $request->user()->assign(UserRole::REGISTERED);
        $request->user()->registerAnalyticsServiceAccount();
        $request->user()->setViewAccessForWebsite($website);
        $request->user()->syncWebsitesPermissionsToAnalyticsService();

        event(new PublicAdministrationRegistered($publicAdministration, $request->user()));
        event(new WebsiteAdded($website));

        return redirect()->route('websites.index')->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia.']); //TODO: put message in lang file
    }

    /**
     * Show the form for creating a new website.
     *
     * @return \Illuminate\View\View the view
     */
    public function create(): View
    {
        $usersPermissionsDatatable = [
            'columns' => [
                ['data' => 'name', 'name' => 'Cognome e nome'],
                ['data' => 'email', 'name' => 'Email'],
                ['data' => 'added_at', 'name' => 'Iscritto dal'],
                ['data' => 'status', 'name' => 'Stato'],
                ['data' => 'checkboxes', 'name' => 'Abilitato'],
                ['data' => 'radios', 'name' => 'Permessi'],
            ],
            'source' => route('websites.users.permissions.data'),
            'caption' => 'Elenco degli utenti presenti su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc']],
        ];

        return view('pages.websites.add')->with($usersPermissionsDatatable);
    }

    /**
     * Store a new website.
     *
     * @param StoreWebsiteRequest $request the request
     *
     * @throws BindingResolutionException if unable to bind to the analytics service
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function store(StoreWebsiteRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $publicAdministration = current_public_administration();

        $analyticsId = app()->make('analytics-service')->registerSite($validatedData['name'] . ' [' . $validatedData['type'] . ']', $validatedData['url'], $publicAdministration->name); //TODO: put string in lang file

        if (empty($analyticsId)) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        $website = Website::create([
            'name' => $validatedData['name'],
            'url' => $validatedData['url'],
            'type' => (int) $validatedData['type'],
            'public_administration_id' => $publicAdministration->id,
            'analytics_id' => $analyticsId,
            'slug' => Str::slug($validatedData['url']),
            'status' => WebsiteStatus::PENDING,
        ]);

        event(new WebsiteAdded($website));

        $publicAdministration->getAdministrators()->map(function ($administrator) use ($website) {
            $administrator->setWriteAccessForWebsite($website);
            $administrator->syncWebsitesPermissionsToAnalyticsService();
        });

        $this->manageWebsitePermissionsOnNonAdministrators($validatedData, $publicAdministration, $website);

        return redirect()->route('websites.index')->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia.']); //TODO: put message in lang file
    }

    /**
     * Check website tracking status.
     *
     * @param Website $website the website to check
     *
     * @return \Illuminate\Http\JsonResponse the JSON response
     */
    public function checkTracking(Website $website): JsonResponse
    {
        try {
            $tokenAuth = current_user_auth_token();
            if ($website->status->is(WebsiteStatus::PENDING)) {
                if ($this->hasActivated($website, $tokenAuth)) {
                    $this->activate($website);

                    event(new WebsiteActivated($website));

                    return response()->json([
                        'result' => 'ok',
                        'id' => $website->slug,
                        'status' => $website->status->description,
                    ]);
                }

                return response()->json(null, 304);
            }

            throw new InvalidWebsiteStatusException('Unable to check activation for website ' . $website->getInfo() . ' in status ' . $website->status->description);
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

        return response()->json(['result' => 'error', 'message' => $message, 'code' => $code], $httpStatusCode);
    }

    /**
     * Archive website request.
     * Only active and not primary type websites can be archived.
     *
     * @param Website $website the website
     *
     * @return JsonResponse the JSON response
     */
    public function archive(Website $website): JsonResponse
    {
        try {
            if (!$website->type->is(WebsiteType::PRIMARY)) {
                if ($website->status->is(WebsiteStatus::ACTIVE)) {
                    $website->status = WebsiteStatus::ARCHIVED;
                    app()->make('analytics-service')->changeArchiveStatus($website->analytics_id, WebsiteStatus::ARCHIVED);
                    $website->save();

                    event(new WebsiteArchived($website));

                    return response()->json([
                        'result' => 'ok',
                        'id' => $website->slug,
                        'status' => $website->status->description,
                    ]);
                }

                if ($website->status->is(WebsiteStatus::ARCHIVED)) {
                    return response()->json(null, 304);
                }

                throw new InvalidWebsiteStatusException('Unable to archive website ' . $website->getInfo() . ' in status ' . $website->status->description);
            }

            throw new OperationNotAllowedException('Archive request not allowed on primary website ' . $website->getInfo());
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

        return response()->json(['result' => 'error', 'message' => $message, 'code' => $code], $httpStatusCode);
    }

    /**
     * Re-enable an archived website.
     * Only archived and not primary type websites can be re-enabled.
     *
     * @param Website $website the website
     *
     * @return JsonResponse the JSON response
     */
    public function unarchive(Website $website): JsonResponse
    {
        try {
            if (!$website->type->is(WebsiteType::PRIMARY)) {
                if ($website->status->is(WebsiteStatus::ARCHIVED)) {
                    $website->status = WebsiteStatus::ACTIVE;
                    app()->make('analytics-service')->changeArchiveStatus($website->analytics_id, WebsiteStatus::ACTIVE);
                    $website->save();

                    event(new WebsiteUnarchived($website));

                    return response()->json([
                        'result' => 'ok',
                        'id' => $website->slug,
                        'status' => $website->status->description,
                    ]);
                }

                if ($website->status->is(WebsiteStatus::ACTIVE)) {
                    return response()->json(null, 304);
                }

                throw new InvalidWebsiteStatusException('Unable to cancel archiving for website ' . $website->getInfo() . ' in status ' . $website->status->description);
            }

            throw new OperationNotAllowedException('Cancel archiving request not allowed on primary website ' . $website->getInfo());
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

        return response()->json(['result' => 'error', 'message' => $message, 'code' => $code], $httpStatusCode);
    }

    /**
     * Display a website information.
     *
     * @param Website $website the website to show
     *
     * @throws BindingResolutionException if unable to bind to the analytics service
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     *
     * @return \Illuminate\View\View the view
     */
    public function show(Website $website): View
    {
        $data = [
            'columns' => [
                ['data' => 'name', 'name' => 'Cognome e nome'],
                ['data' => 'email', 'name' => 'Email'],
                ['data' => 'added_at', 'name' => 'Iscritto dal'],
                ['data' => 'status', 'name' => 'Stato'],
                ['data' => 'checkboxes', 'name' => 'Abilitato'],
                ['data' => 'radios', 'name' => 'Permessi'],
            ],
            'source' => route('websites.users.permissions.data', ['website' => $website]) . '?readOnly=true',
            'caption' => 'Elenco degli utenti presenti su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc']],
            'website' => $website,
        ];

        if (!$website->status->is(WebsiteStatus::PENDING) && auth()->user()->can(UserPermission::READ_ANALYTICS, $website)) {
            $data['lastMonthVisits'] = (int) app()->make('analytics-service')->getSiteLastMonthVisits($website->analytics_id, current_user_auth_token());
        }

        return view('pages.websites.show')->with($data);
    }

    /**
     * Show the form for editing a website.
     *
     * @param Website $website the website to edit
     *
     * @return \Illuminate\View\View the view
     */
    public function edit(Website $website): View
    {
        $usersPermissionsDatatable = [
            'columns' => [
                ['data' => 'name', 'name' => 'Cognome e nome'],
                ['data' => 'email', 'name' => 'Email'],
                ['data' => 'added_at', 'name' => 'Iscritto dal'],
                ['data' => 'status', 'name' => 'Stato'],
                ['data' => 'checkboxes', 'name' => 'Abilitato'],
                ['data' => 'radios', 'name' => 'Permessi'],
            ],
            'source' => route('websites.users.permissions.data', ['website' => $website]),
            'caption' => 'Elenco degli utenti presenti su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc']],
        ];

        return view('pages.websites.edit')->with(['website' => $website])->with($usersPermissionsDatatable);
    }

    /**
     * Update a website.
     * NOTE: for primary websites, only updates to user permissions are allowed.
     *
     * @param UpdateWebsiteRequest $request the request
     * @param Website $website the website to update
     *
     * @throws BindingResolutionException if unable to bind to the analytics service
     * @throws CommandErrorException if command is unsuccessful
     * @throws AnalyticsServiceException if unable to connect the Analytics Service
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function update(UpdateWebsiteRequest $request, Website $website): RedirectResponse
    {
        $validatedData = $request->validated();

        if (!$website->type->is(WebsiteType::PRIMARY)) {
            if ($website->slug !== Str::slug($validatedData['url'])) {
                app()->make('analytics-service')->updateSite($website->analytics_id, $validatedData['name'] . ' [' . $validatedData['type'] . ']', $validatedData['url'], $website->publicAdministration->name); //TODO: put string in lang file
            }

            $website->fill([
                'name' => $validatedData['name'],
                'url' => $validatedData['url'],
                'type' => $validatedData['type'],
                'slug' => Str::slug($validatedData['url']),
            ]);
            $website->save();
        }

        if (null !== ($publicAdministration = current_public_administration())) {
            $this->manageWebsitePermissionsOnNonAdministrators($validatedData, $publicAdministration, $website);
        }

        return redirect()->route('websites.index')->withMessage(['success' => 'Il sito "' . $website->getInfo() . '" è stato modificato.']); //TODO: put message in lang file
    }

    /**
     * Delete a website.
     * NOTE: super-admin only.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to delete
     *
     * @throws \Exception if unable to delete the website
     *
     * @return JsonResponse the JSON response
     */
    public function delete(PublicAdministration $publicAdministration, Website $website): JsonResponse
    {
        try {
            if ($website->type->is(WebsiteType::PRIMARY)) {
                throw new OperationNotAllowedException('Impossibile eliminare un sito istituzionale');
            }

            app()->make('analytics-service')->changeArchiveStatus($website->analytics_id, WebsiteStatus::ARCHIVED);
            $website->delete();

            return response()->json(['result' => 'ok', 'id' => $website->slug, 'status' => $website->status->description]);
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

        return response()->json(['result' => 'error', 'message' => $message, 'code' => $code], $httpStatusCode);
    }

    /**
     * Restore a deleted website.
     * NOTE: super-admin only.
     *
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param Website $website the website to restore
     *
     * @return JsonResponse the JSON response
     */
    public function restore(PublicAdministration $publicAdministration, Website $website): JsonResponse
    {
        try {
            if (!$website->trashed()) {
                return response()->json(null, 304);
            }

            //NOTE: 'ACTIVE' status is for re-enabling tracking on Analytics Service: actual website status on WAI isn't changed
            app()->make('analytics-service')->changeArchiveStatus($website->analytics_id, WebsiteStatus::ACTIVE);
            $website->restore();

            return response()->json(['result' => 'ok', 'id' => $website->slug, 'status' => $website->status->description]);
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

        return response()->json(['result' => 'error', 'message' => $message, 'code' => $code], $httpStatusCode);
    }

    /**
     * Get the websites data.
     *
     * @param PublicAdministration|null $publicAdministration the P.A. to filter websites or null to use current user P.A.
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response the JSON format
     */
    public function dataJson(PublicAdministration $publicAdministration)
    {
        return Datatables::of(auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? $publicAdministration->websites()->withTrashed()->get() : current_public_administration()->websites())
            ->setTransformer(new WebsiteTransformer())
            ->make(true);
    }

    /**
     * Get the user permissions on a website.
     *
     * @param Website|null $website the website to use for permissions initialization or null for default
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response the JSON format
     */
    public function dataUsersPermissionsJson(Website $website)
    {
        return Datatables::of(current_public_administration()->users()->where('status', '!=', UserStatus::SUSPENDED))
            ->setTransformer(new UsersPermissionsTransformer())
            ->make(true);
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
     * @return \Illuminate\View\View the view
     */
    public function showJavascriptSnippet(Website $website): View
    {
        $tokenAuth = current_user_auth_token();
        $javascriptSnippet = app()->make('analytics-service')->getJavascriptSnippet($website->analytics_id, $tokenAuth);

        return view('pages.websites.javascript_snippet')->with(['javascriptSnippet' => trim($javascriptSnippet)]);
    }

    /**
     * Manage non-admin users permissions on P.A. website.
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
        $usersEnabled = $validatedData['usersEnabled'] ?? [];
        $usersPermissions = $validatedData['usersPermissions'] ?? [];
        $publicAdministration->getNonAdministrators()->map(function ($user) use ($website, $usersEnabled, $usersPermissions) {
            if (!empty($usersPermissions[$user->id]) && UserPermission::MANAGE_ANALYTICS === $usersPermissions[$user->id]) {
                $user->setWriteAccessForWebsite($website);
            }

            if (!empty($usersPermissions[$user->id]) && UserPermission::READ_ANALYTICS === $usersPermissions[$user->id]) {
                $user->setViewAccessForWebsite($website);
            }

            if (empty($usersEnabled[$user->id])) {
                $user->setNoAccessForWebsite($website);
            }

            if ($user->hasAnalyticsServiceAccount()) {
                $user->syncWebsitesPermissionsToAnalyticsService();
            }
        });
    }
}
