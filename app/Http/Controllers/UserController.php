<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserInvited;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Transformers\WebsitesPermissionsTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
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
            'source' => route('users.data.json'),
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

        $isAdmin = $request->input('isAdmin', false);
        $websitesEnabled = $request->input('websitesEnabled', []);
        $websitesPermissions = $request->input('websitesPermissions', []);
        $currentPublicAdministration->websites->map(function ($website) use ($user, $isAdmin, $websitesEnabled, $websitesPermissions) {
            if ($isAdmin) {
                $user->assign(UserRole::ADMIN);
                $user->setWriteAccessForWebsite($website);
            } else {
                $user->assign(UserRole::DELEGATED);
                if (!empty($websitesPermissions[$website->id]) && UserPermission::MANAGE_ANALYTICS === $websitesPermissions[$website->id]) {
                    $user->setWriteAccessForWebsite($website);
                }

                if (!empty($websitesPermissions[$website->id]) && UserPermission::READ_ANALYTICS === $websitesPermissions[$website->id]) {
                    $user->setViewAccessForWebsite($website);
                }

                if (empty($websitesEnabled[$website->id])) {
                    $user->setNoAccessForWebsite($website);
                }
            }
        });

        $user->syncWebsitesPermissionsToAnalyticsService();

        event(new UserInvited($user, $request->user(), current_public_administration()));

        return redirect()->route('users.index')->withMessage(['success' => 'Il nuovo utente è stato invitato al progetto Web Analytics Italia']); //TODO: put message in lang file
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
        $data = [
            'columns' => [
                ['data' => 'url', 'name' => 'URL'],
                ['data' => 'type', 'name' => 'Tipo', 'visible' => false],
                ['data' => 'added_at', 'name' => 'Aggiunto il', 'visible' => false],
                ['data' => 'status', 'name' => 'Stato', 'visible' => false],
                ['data' => 'checkboxes', 'name' => 'Abilitato'],
                ['data' => 'radios', 'name' => 'Permessi'],
            ],
            'source' => route('users.websites.permissions.data', ['user' => $user]) . '?readOnly=true',
            'caption' => 'Elenco dei siti web presenti su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc']],
            'user' => $user,
        ];

        return view('pages.users.show')->with($data);
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
        $data = [
            'columns' => [
                ['data' => 'url', 'name' => 'URL'],
                ['data' => 'type', 'name' => 'Tipo'],
                ['data' => 'added_at', 'name' => 'Aggiunto il'],
                ['data' => 'status', 'name' => 'Stato'],
                ['data' => 'checkboxes', 'name' => 'Abilitato'],
                ['data' => 'radios', 'name' => 'Permessi'],
            ],
            'source' => route('users.websites.permissions.data', ['user' => $user]),
            'caption' => 'Elenco dei siti web presenti su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc']],
            'user' => $user,
        ];

        return view('pages.users.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validatedData = $request->validated();

        // Update user information
        if ($user->email !== $validatedData['email']) {
            // NOTE: the 'user update' event listener automatically
            //      sends a new email verification request and
            //      reset the email verification status
            $user->email = $validatedData['email'];
            $user->save();

            //NOTE: remove the try/catch if matomo is configured
            //      to not send email on user updates using API interface
            //      See: https://github.com/matomo-org/matomo/pull/14281
            try {
                // Update Analytics Service account if needed
                // NOTE: at this point, user must have an analytics account
                $user->updateAnalyticsServiceAccountEmail();
            } catch (CommandErrorException $exception) {
                if (!Str::contains($exception->getMessage(), 'Unable to send mail.')) {
                    throw $exception;
                }
            }
        }

        // Update permissions
        // NOTE: at this point, user must have an analytics account
        $isAdmin = $validatedData['isAdmin'] ?? false;
        $websitesEnabled = $validatedData['websitesEnabled'] ?? [];
        $websitesPermissions = $validatedData['websitesPermissions'] ?? [];

        current_public_administration()->websites->map(function ($website) use ($user, $isAdmin, $websitesEnabled, $websitesPermissions) {
            if ($isAdmin) {
                $user->retract(UserRole::DELEGATED);
                $user->assign(UserRole::ADMIN);
                $user->setWriteAccessForWebsite($website);
            } else {
                $user->retract(UserRole::ADMIN);
                $user->assign(UserRole::DELEGATED);
                if (!empty($websitesPermissions[$website->id]) && UserPermission::MANAGE_ANALYTICS === $websitesPermissions[$website->id]) {
                    $user->setWriteAccessForWebsite($website);
                }

                if (!empty($websitesPermissions[$website->id]) && UserPermission::READ_ANALYTICS === $websitesPermissions[$website->id]) {
                    $user->setViewAccessForWebsite($website);
                }

                if (empty($websitesEnabled[$website->id])) {
                    $user->setNoAccessForWebsite($website);
                }
            }
        });

        $user->syncWebsitesPermissionsToAnalyticsService();

        return redirect()->route('users.index')->withMessage(['success' => "L'utente " . $user->getInfo() . ' è stato modificato.']); //TODO: put message in lang file
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
    public function dataWebsitesPermissionsJson(User $user)
    {
        return Datatables::of(current_public_administration()->websites)
            ->setTransformer(new WebsitesPermissionsTransformer())
            ->make(true);
    }

    public function validateNotLastActiveAdministrator(Validator $validator): void
    {
        $publicAdministration = request()->route('publicAdministration');
        $user = request()->route('user');
        $isAdmin = Bouncer::scope()->onceTo($publicAdministration ? $publicAdministration->id : current_public_administration()->id, function () use ($user) {
            return $user->isA(UserRole::ADMIN);
        });
        if ($isAdmin && $user->status->is(UserStatus::ACTIVE) && 1 === ($publicAdministration ?? current_public_administration())->getActiveAdministrators()->count()) {
            $validator->errors()->add('isAdmin', 'Impossibile rimuovere l\'utente ' . $user->getInfo() . ' in quanto ultimo amministratore attivo della P.A.');
        }
    }
}
