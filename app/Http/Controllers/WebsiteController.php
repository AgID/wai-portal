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
use App\Http\Requests\StorePrimaryWebsiteRequest;
use App\Http\Requests\StoreWebsiteRequest;
use App\Models\PublicAdministration;
use App\Models\Website;
use App\Traits\ActivatesWebsite;
use App\Transformers\UsersPermissionsTransformer;
use App\Transformers\WebsiteTransformer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Yajra\Datatables\Datatables;

class WebsiteController extends Controller
{
    use ActivatesWebsite;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $websitesDatatable = [
            'columns' => [
                ['data' => 'url', 'name' => 'URL'],
                ['data' => 'type', 'name' => 'Tipo'],
                ['data' => 'added_at', 'name' => 'Aggiunto il'],
                ['data' => 'status', 'name' => 'Stato'],
                ['data' => 'last_month_visits', 'name' => 'Visite*'],
                ['data' => 'buttons', 'name' => 'Azioni'],
            ],
            'source' => route('websites-data-json'),
            'caption' => 'Elenco dei siti web abilitati su Web Analytics Italia', //TODO: set title in lang file
            'footer' => '*Il numero di visite si riferisce agli ultimi 30 giorni.',
            'columnsOrder' => [['added_at', 'asc'], ['last_month_visits', 'desc']],
        ];

        return view('pages.websites.index')->with($websitesDatatable);
    }

    public function createPrimary()
    {
        if (auth()->user()->publicAdministrations->isNotEmpty()) {
            return redirect()->route('websites-index');
        }

        return view('pages.websites.add_primary');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePrimaryWebsiteRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function storePrimary(StorePrimaryWebsiteRequest $request)
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

        if (empty($analyticsId)) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

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

        return redirect()->route('websites-index')->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia.']); //TODO: put message in lang file
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
     * Store a newly created resource in storage.
     *
     * @param StoreWebsiteRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWebsiteRequest $request)
    {
        $publicAdministration = current_public_administration();

        $analyticsId = app()->make('analytics-service')->registerSite($request->input('name') . ' [' . $request->input('type') . ']', $request->input('url'), $publicAdministration->name); //TODO: put string in lang file

        if (empty($analyticsId)) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        $website = Website::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'type' => (int) $request->input('type'),
            'public_administration_id' => $publicAdministration->id,
            'analytics_id' => $analyticsId,
            'slug' => Str::slug($request->input('url')),
            'status' => WebsiteStatus::PENDING,
        ]);

        event(new WebsiteAdded($website));

        $publicAdministration->getAdministrators()->map(function ($administrator) use ($website) {
            $administrator->setWriteAccessForWebsite($website);
            $administrator->syncWebsitesPermissionsToAnalyticsService();
        });
        $usersEnabled = $request->input('usersEnabled', []);
        $usersPermissions = $request->input('usersPermissions', []);
        $publicAdministration->getNotAdministrators()->map(function ($user) use ($website, $usersEnabled, $usersPermissions) {
            if (!empty($usersPermissions[$user->id]) && UserPermission::MANAGE_ANALYTICS === $usersPermissions[$user->id]) {
                $user->setWriteAccessForWebsite($website);
            }

            if (!empty($usersPermissions[$user->id]) && UserPermission::READ_ANALYTICS === $usersPermissions[$user->id]) {
                $user->setViewAccessForWebsite($website);
            }

            if (empty($usersEnabled[$user->id])) {
                $user->setNoAccessForWebsite($website);
            }

            if ($user->status->is(UserStatus::ACTIVE)) {
                $user->syncWebsitesPermissionsToAnalyticsService();
            }
        });

        return redirect()->route('websites-index')->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia.']); //TODO: put message in lang file
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
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Website $website
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Website $website)
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

        if ($website->type->is(WebsiteType::PRIMARY)) {
            abort(403, 'Non è permesso effettuare modifiche al sito istituzionale.');
        }

        return view('pages.websites.edit')->with(['website' => $website])->with($usersPermissionsDatatable);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Website $website
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Website $website)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'url' => [
                'required',
                Rule::unique('websites')->ignore($website->id),
                'url',
            ],
            'type' => 'required|in:secondary,webapp,testing',
        ]);

        $tokenAuth = current_user_auth_token();
        $updated = app()->make('analytics-service')->updateSite($website->analytics_id, $validatedData['name'] . ' [' . $validatedData['type'] . ']', $validatedData['url'], $website->publicAdministration->name, $tokenAuth); //TODO: put string in lang file

        if (!$updated) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        $website->fill([
            'name' => $validatedData['name'],
            'url' => $validatedData['url'],
            'type' => $validatedData['type'],
            'slug' => Str::slug($validatedData['url']),
        ]);
        $website->save();

        logger()->info('User ' . auth()->user()->uuid . ' updated website "' . $validatedData['name'] . '" [' . $validatedData['url'] . '] as ' . $validatedData['type'] . ' website of "' . $website->publicAdministration->name . '"');

        return redirect()->route('websites-index')->withMessage(['success' => 'Il sito "' . $validatedData['name'] . '" è stato modificato.']); //TODO: put message in lang file
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Get all websites of the specified Public Administration
     * in JSON format (to be consumed by Datatables).
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function dataJson()
    {
        return Datatables::of(current_public_administration()->websites())
            ->setTransformer(new WebsiteTransformer())
            ->make(true);
    }

    /**
     * Get all users of the specified Public Administration
     * in JSON format (to be consumed by Datatables).
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function dataUsersPermissionsJson()
    {
        return Datatables::of(current_public_administration()->users)
            ->setTransformer(new UsersPermissionsTransformer())
            ->make(true);
    }

    /**
     * Get Javascript snippet for the specified Website
     * of the specified Public Administration.
     *
     * @param Website $website
     *
     * @return $this
     */
    public function showJavascriptSnippet(Website $website)
    {
        $tokenAuth = current_user_auth_token();
        $javascriptSnippet = app()->make('analytics-service')->getJavascriptSnippet($website->analytics_id, $tokenAuth);

        return view('pages.websites.javascript_snippet')->with(['javascriptSnippet' => trim($javascriptSnippet)]);
    }
}
