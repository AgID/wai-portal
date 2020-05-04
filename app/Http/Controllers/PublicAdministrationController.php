<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use App\Traits\HasRoleAwareUrls;
use App\Traits\SendsResponse;
use App\Transformers\PublicAdministrationsTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Yajra\DataTables\Facades\DataTables;

class PublicAdministrationController extends Controller
{
    use HasRoleAwareUrls;
    use SendsResponse;

    /**
     * Show the Public Administration selector.
     *
     * @param int $id
     *
     * @return View the view
     */
    public function selectTenant(): View
    {
        return view('pages.pa.select');
    }

    /**
     * Change the Public Administration tenant in the session.
     *
     * @param Request the incoming request
     *
     * @return \Illuminate\Http\Response
     */
    public function changeTenant(Request $request, Website $website)
    {
        $publicAdministrationCode = $request->input('public-administration-nav');
        $redirectTo = $request->input('target-route');
        $targetRouteHasPublicAdministrationParam = $request->input('target-route-pa-param');

        if (!Route::has($redirectTo)) {
            abort(404);
        }

        // publicAdministrationCode is ipa_code for superAdmin, id for other roles
        $authUser = $request->user();

        if ($authUser->isA(UserRole::SUPER_ADMIN)) {
            if (PublicAdministration::where('ipa_code', $publicAdministrationCode)->first()) {
                session()->put('super_admin_tenant_ipa_code', $publicAdministrationCode);
                if (!$targetRouteHasPublicAdministrationParam) {
                    return redirect()->route($redirectTo);
                }

                return redirect()->route($redirectTo, ['publicAdministration' => $publicAdministrationCode]);
            }
        } elseif ($authUser->publicAdministrations->isNotEmpty()) {
            if ($authUser->publicAdministrations()->where('id', $publicAdministrationCode)->first()) {
                session()->put('tenant_id', $publicAdministrationCode);
                Bouncer::scope()->to($publicAdministrationCode);

                return redirect()->route($redirectTo);
            }
        }

        return redirect()->route('home');
    }

    /**
     * Add new Public Administrations for current user.
     *
     * @return View the view
     */
    public function add(): View
    {
        return view('pages.pa.add');
    }

    /**
     * Show all Public Administrations for current user.
     *
     * @return View the view
     */
    public function show(PublicAdministration $publicAdministration): View
    {
        $paDatatable = [
            'datatableOptions' => [
                'columnFilters' => [
                    'name' => [
                        'filterLabel' => __('nome'),
                    ],
                    'status' => [
                        'filterLabel' => __('stato'),
                    ],
                ],
            ],
            'columns' => [
                ['data' => 'name', 'name' => __('nome'), 'className' => 'text-wrap'],
                ['data' => 'city', 'name' => __('città')],
                ['data' => 'region', 'name' => __('regione')],
                ['data' => 'email', 'name' => __('email')],
                ['data' => 'status', 'name' => __('stato')],
                ['data' => 'buttons', 'name' => '', 'orderable' => false],
            ],
            'source' => $this->getRoleAwareUrl('publicAdministrations.data.json', [], $publicAdministration),
            'caption' => __('elenco delle tue pubbliche amministrazioni su :app', ['app' => config('app.name')]),
            'columnsOrder' => [['name', 'asc']],
        ];

        return view('pages.pa.index')->with($paDatatable);
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param Request $request the incoming request
     * @param string $uuid the uuid of the user to be verified
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException if verification link is invalid
     *
     * @return JsonResponse|RedirectResponse the server response
     */
    public function activate(Request $request, string $uuid, PublicAdministration $publicAdministration)
    {
        $user = User::where('uuid', $uuid)->with('publicAdministrations')->first();
        $authUser = $request->user();

        if (!$authUser->is($user) || !$user->publicAdministrations->contains($publicAdministration)) {
            throw new AuthorizationException("L'utente corrente non corrisponde alla richiesta di verifica.");
        }

        if ($user->invitedPublicAdministrations->where('id', $publicAdministration->id)->isNotEmpty()) {
            $user->publicAdministrations()->updateExistingPivot($publicAdministration->id, ['user_status' => UserStatus::ACTIVE]);

            return $this->publicAdministrationResponse($publicAdministration);
        }

        return $this->notModifiedResponse();
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
    public function dataJson()
    {
        return DataTables::of(auth()->user()->publicAdministrations)
            ->setTransformer(new PublicAdministrationsTransformer())
            ->make(true);
    }
}
