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
use Illuminate\Support\Facades\Hash;
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
     * @return \Illuminate\Http\Response
     */
    public function selectTenant()
    {
        return view('pages.pa.select');
    }

    /**
     * Change the Public Administration.
     *
     * @param Request the incoming request
     *
     * @return \Illuminate\Http\Response
     */
    public function changeTenant(Request $request, Website $website)
    {
        $publicAdministrationCode = $request->input('public-administration-nav');
        $redirect = $request->input('return-route');
        $hasRouteParampublicAdministration = $request->input('has-route-param-pa');
        // publicAdministrationCode is ipa_code for superAdmin, id for other roles
        $authUser = auth()->user();
        if ($authUser->isA(UserRole::SUPER_ADMIN)) {
            if (PublicAdministration::where('ipa_code', $publicAdministrationCode)->first()) {
                session()->put('super_admin_tenant_ipa_code', $publicAdministrationCode);
                if (!$hasRouteParampublicAdministration) {
                    return redirect()->route($redirect);
                }

                return redirect()->route($redirect, ['publicAdministration' => $publicAdministrationCode]);
            }
        } elseif ($authUser->publicAdministrations->isNotEmpty()) {
            if ($authUser->publicAdministrations()->where('id', $publicAdministrationCode)->first()) {
                session()->put('tenant_id', $publicAdministrationCode);
                Bouncer::scope()->to($publicAdministrationCode);

                return redirect()->route($redirect);
            }
        }

        return redirect()->route('home');
    }

    /**
     * Add new Public Administrations for current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function add(): View
    {
        return view('pages.pa.add');
    }

    /**
     * Show all Public Administrations for current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(PublicAdministration $publicAdministration)
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
     * @param string $hash the hash of the user email address
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to SPID service
     * @throws \Illuminate\Auth\Access\AuthorizationException if verification link is invalid
     *
     * @return RedirectResponse the server redirect response
     */
    public function activation(Request $request, string $uuid, string $pa): JsonResponse
    {
        $user = User::where('uuid', $uuid)->first();

        if (!$user) {
            throw new AuthorizationException('Current user does not match.');
        }

        $publicAdministrationConfirmed = $user->publicAdministrations()->where('pa_status', UserStatus::INVITED)->get()->map(function ($publicAdministration) use ($pa, $user) {
            if (Hash::check($publicAdministration->id, base64_decode($pa, true))) {
                $publicAdministration->users()->syncWithoutDetaching([$user->id => ['pa_status' => UserStatus::ACTIVE]]);

                return $publicAdministration;
            }
        });

        if ($publicAdministrationConfirmed->isNotEmpty()) {
            $publicAdministration = PublicAdministration::find($publicAdministrationConfirmed->first()->id);

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
     * @return mixed the response the JSON format
     */
    public function dataJson()
    {
        return DataTables::of(auth()->user()->publicAdministrations()->get())
            ->setTransformer(new PublicAdministrationsTransformer())
            ->make(true);
    }
}
