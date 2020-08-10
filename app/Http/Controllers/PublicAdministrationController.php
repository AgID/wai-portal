<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\PublicAdministration;
use App\Models\User;
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
                    'userStatus' => [
                        'filterLabel' => __('stato utente'),
                    ],
                ],
            ],
            'columns' => [
                ['data' => 'name', 'name' => __('nome'), 'className' => 'text-wrap'],
                ['data' => 'email', 'name' => __('email')],
                ['data' => 'userStatus', 'name' => __('stato utente')],
                ['data' => 'buttons', 'name' => '', 'orderable' => false],
            ],
            'source' => $this->getRoleAwareUrl('publicAdministrations.data.json', [], $publicAdministration),
            'caption' => __('elenco delle tue pubbliche amministrazioni su :app', ['app' => config('app.name')]),
            'columnsOrder' => [['name', 'asc']],
        ];

        return view('pages.pa.index')->with($paDatatable)->with('hasPublicAdministrations', auth()->user()->publicAdministrationsWithSuspended->isNotEmpty());
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
     * Change the Public Administration tenant in the session.
     *
     * @param Request the incoming request
     *
     * @return \Illuminate\Http\Response
     */
    public function selectTenant(Request $request)
    {
        $publicAdministration = PublicAdministration::where('ipa_code', $request->input('public-administration'))->firstOrFail();
        $redirectTo = $request->input('target-route') ?? 'analytics';
        $targetRouteHasPublicAdministrationParam = $request->input('target-route-pa-param') ?? false;

        if (!Route::has($redirectTo)) {
            abort(404);
        }

        $authUser = $request->user();

        if ($authUser->isA(UserRole::SUPER_ADMIN)) {
            if ($publicAdministration->ipa_code) {
                session()->put('super_admin_tenant_ipa_code', $publicAdministration->ipa_code);
                if (!$targetRouteHasPublicAdministrationParam) {
                    return redirect()->route($redirectTo);
                }

                return redirect()->route($redirectTo, ['publicAdministration' => $publicAdministration]);
            }
        } elseif ($authUser->publicAdministrations->isNotEmpty()) {
            if ($authUser->publicAdministrations->contains($publicAdministration)) {
                session()->put('tenant_id', $publicAdministration->id);
                Bouncer::scope()->to($publicAdministration->id);

                return redirect()->route($redirectTo);
            }
        }

        return redirect()->route('home');
    }

    /**
     * Accept an invitation a user has got for a public administration.
     *
     * @param Request $request the incoming request
     * @param PublicAdministration $publicAdministration the public administration the user is invited to
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException if verification link is invalid
     *
     * @return JsonResponse|RedirectResponse the server response
     */
    public function acceptInvitation(Request $request, PublicAdministration $publicAdministration)
    {
        $authUser = $request->user();

        if (!$authUser->publicAdministrations->contains($publicAdministration)) {
            throw new AuthorizationException("L'utente corrente non corrisponde all'invito.");
        }

        if ($authUser->invitedPublicAdministrations->where('id', $publicAdministration->id)->isNotEmpty()) {
            $authUser->publicAdministrations()->updateExistingPivot($publicAdministration->id, ['user_status' => UserStatus::ACTIVE]);

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
        return DataTables::of(auth()->user()->publicAdministrationsWithSuspended)
            ->setTransformer(new PublicAdministrationsTransformer())
            ->make(true);
    }
}
