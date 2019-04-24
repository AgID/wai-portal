<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Enums\UserStatus;
use App\Events\User\UserInvited;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Transformers\WebsitesPermissionsTransformer;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Yajra\Datatables\Datatables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usersDatatable = [
            'columns' => [
                ['data' => 'name', 'name' => 'Cognome e nome'],
                ['data' => 'email', 'name' => 'Email'],
                ['data' => 'admin', 'name' => 'Amministratore'],
                ['data' => 'added_at', 'name' => 'Iscritto dal'],
                ['data' => 'status', 'name' => 'Stato'],
                ['data' => 'buttons', 'name' => 'Azioni'],
            ],
            'source' => route('users-data-json'),
            'caption' => 'Elenco degli utenti web abilitati su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc'], ['name', 'asc']],
        ];

        return view('pages.users.index')->with($usersDatatable);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $websitesPermissionsDatatable = [
            'columns' => [
                ['data' => 'url', 'name' => 'URL'],
                ['data' => 'type', 'name' => 'Tipo'],
                ['data' => 'added_at', 'name' => 'Aggiunto il'],
                ['data' => 'status', 'name' => 'Stato'],
                ['data' => 'checkboxes', 'name' => 'Abilitato'],
                ['data' => 'radios', 'name' => 'Permessi'],
            ],
            'source' => route('users.websites.permissions.data'),
            'caption' => 'Elenco dei siti web presenti su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc']],
        ];

        return view('pages.users.add')->with($websitesPermissionsDatatable);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $currentPublicAdministration = current_public_administration();

        $user = User::create([
            'uuid' => Uuid::uuid4()->toString(),
            'fiscalNumber' => $request->input('fiscalNumber'),
            'email' => $request->input('email'),
            'status' => UserStatus::INVITED,
        ]);
        $user->publicAdministrations()->attach($currentPublicAdministration->id);

        $user->registerAnalyticsServiceAccount();

        $isAdmin = $request->input('isAdmin');
        $websitesEnabled = $request->input('websitesEnabled') ?? [];
        $websitesPermissions = $request->input('websitesPermissions') ?? [];
        $currentPublicAdministration->websites->map(function ($website) use ($user, $isAdmin, $websitesEnabled, $websitesPermissions) {
            if (!empty($isAdmin) && $isAdmin) {
                $user->setWriteAccessForWebsite($website);
            }

            if (!empty($websitesPermissions[$website->id]) && UserPermission::MANAGE_ANALYTICS === $websitesPermissions[$website->id]) {
                $user->setWriteAccessForWebsite($website);
            }

            if (!empty($websitesPermissions[$website->id]) && UserPermission::READ_ANALYTICS === $websitesPermissions[$website->id]) {
                $user->setViewAccessForWebsite($website);
            }

            if (empty($websitesEnabled[$website->id])) {
                $user->setNoAccessForWebsite($website);
            }
        });

        $user->syncWebsitesPermissionsToAnalyticsService();

        event(new UserInvited($user, current_public_administration(), $request->user()));

        return redirect()->route('users-index')->withMessage(['success' => 'Il nuovo utente è stato invitato al progetto Web Analytics Italia']); //TODO: put message in lang file
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('pages.users.edit')->with(['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = validator($request->all(), [
            'role' => 'required|exists:roles,name',
        ]);

        $validator->after(function ($validator) use ($user, $request) {
            $lastAdministrator = 1 == current_public_administration()->users()->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->count();
            if ('admin' == $user->roles()->first()->name && 'admin' != $request->input('role') && $lastAdministrator) {
                $validator->errors()->add('role', 'Deve restare almeno un utente amministratore per ogni PA.'); //TODO: put error message in lang file
            }
        });

        if ($validator->fails()) {
            return redirect()->route('users-edit', ['user' => $user])
                ->withErrors($validator)
                ->withInput();
        }

        $user->save();

        $user->assign($request->input('role'));

        logger()->info('User ' . auth()->user()->getInfo() . ' updated user ' . $user->getInfo());

        return redirect()->route('users-index')->withMessage(['success' => "L'utente " . $user->getInfo() . ' è stato modificato.']); //TODO: put message in lang file
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
        return Datatables::of(current_public_administration()->users)
            ->setTransformer(new UserTransformer())
            ->make(true);
    }

    /**
     * Get all websites of the specified Public Administration
     * in JSON format (to be consumed by Datatables).
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function dataWebsitesPermissionsJson()
    {
        return Datatables::of(current_public_administration()->websites)
            ->setTransformer(new WebsitesPermissionsTransformer())
            ->make(true);
    }
}
