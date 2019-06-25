<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserDeleted;
use App\Events\User\UserInvited;
use App\Exceptions\CommandErrorException;
use App\Exceptions\InvalidUserStatusException;
use App\Exceptions\OperationNotAllowedException;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Transformers\UserTransformer;
use App\Transformers\WebsitesPermissionsTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Yajra\Datatables\Datatables;

/**
 * User management controller.
 */
class UserController extends Controller
{
    /**
     * Display super-admin user list.
     *
     * @return \Illuminate\View\View the view
     */
    public function index(): View
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
            'source' => request()->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? route('admin.publicAdministration.users.data.json', ['publicAdministration' => request()->route('publicAdministration')]) : route('users.data.json'),
            'caption' => 'Elenco degli utenti web abilitati su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc'], ['name', 'asc']],
        ];

        return view('pages.users.index')->with($usersDatatable);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View the view
     */
    public function create(): View
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
     * Create a new user.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     *
     * @throws \Exception if unable to generate user UUID
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function store(StoreUserRequest $request): RedirectResponse
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
     * Show the user details page.
     *
     * @param User $user the user to display
     *
     * @return \Illuminate\View\View the view
     */
    public function show(User $user): View
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
     * Show the form to edit an existing user.
     *
     * @param User $user the user to edit
     *
     * @return \Illuminate\View\View the view
     */
    public function edit(User $user): View
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
     * Update the user information.
     *
     * @param UpdateUserRequest $request the incoming request
     * @param User $user the user to update
     *
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \App\Exceptions\AnalyticsServiceAccountException if the Analytics Service account doesn't exist
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\TenantIdNotSetException if the tenant id is not set in the current session
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
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
     * Suspend an existing user.
     *
     * @param User $user the user to suspend
     *
     * @return \Illuminate\Http\JsonResponse the JSON response
     */
    public function suspend(User $user): JsonResponse
    {
        try {
            if ($user->status->is(UserStatus::SUSPENDED)) {
                return response()->json(null, 304);
            }

            if ($user->status->is(UserStatus::PENDING)) {
                throw new InvalidUserStatusException('Impossibile sospendere un utente in attesa di attivazione'); //TODO: put message in lang file
            }

            $validator = validator(request()->all())->after([$this, 'validateNotLastActiveAdministrator']);
            if ($validator->fails()) {
                throw new OperationNotAllowedException($validator->errors()->first('isAdmin'));
            }

            $user->status = UserStatus::SUSPENDED;
            $user->save();

            $user->publicAdministrations()->get()->map(function ($publicAdministration) use ($user) {
                Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
                    $user->assign(UserRole::REMOVED);
                });
            });

            return response()->json(['result' => 'ok', 'id' => $user->uuid, 'status' => $user->status->description]);
        } catch (InvalidUserStatusException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current user status';
            $httpStatusCode = 400;
        } catch (OperationNotAllowedException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current user';
            $httpStatusCode = 400;
        }

        return response()->json(['result' => 'error', 'message' => $message, 'code' => $code], $httpStatusCode);
    }

    /**
     * Reactivate an existing suspended user.
     *
     * @param User $user the user to reactivate
     *
     * @return \Illuminate\Http\JsonResponse the JSON response
     */
    public function reactivate(User $user): JsonResponse
    {
        if (!$user->status->is(UserStatus::SUSPENDED)) {
            return response()->json(null, 304);
        }

        $user->status = $user->hasVerifiedEmail() ? UserStatus::ACTIVE : UserStatus::INVITED;
        $user->save();

        $user->publicAdministrations()->get()->map(function ($publicAdministration) use ($user) {
            Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
                $user->retract(UserRole::REMOVED);
            });
        });

        return response()->json(['result' => 'ok', 'id' => $user->uuid, 'status' => $user->status->description]);
    }

    /**
     * Remove a user.
     * NOTE: Super-admin only.
     *
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     * @param User $user the user to delete
     *
     * @throws \Exception if unable to delete
     *
     * @return \Illuminate\Http\JsonResponse the JSON response
     */
    public function delete(PublicAdministration $publicAdministration, User $user): JsonResponse
    {
        try {
            if ($user->trashed()) {
                return response()->json(null, 304);
            }

            if ($user->status->is(UserStatus::PENDING)) {
                throw new OperationNotAllowedException('Impossibile rimuovere un utente in attesa di attivazione'); //TODO: put message in lang file
            }

            $validator = validator(request()->all())->after([$this, 'validateNotLastActiveAdministrator']);
            if ($validator->fails()) {
                throw new OperationNotAllowedException($validator->errors()->first('isAdmin'));
            }

            $user->publicAdministrations()->get()->map(function ($publicAdministration) use ($user) {
                Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
                    $user->assign(UserRole::REMOVED);
                });
            });

            // NOTE: don't use 'user->delete()' directly since
            //       it cascades delete to roles and permissions
            // See: https://github.com/JosephSilber/bouncer/issues/439
            $user->deleted_at = $user->freshTimestamp();
            $user->save();
        } catch (OperationNotAllowedException $exception) {
            report($exception);

            return response()->json(['result' => 'error', 'message' => $exception->getMessage()], 400);
        }
        event(new UserDeleted($user));

        return response()->json(['result' => 'ok', 'id' => $user->uuid]);
    }

    /**
     * Restore a soft-deleted user.
     * NOTE: Super-admin only.
     *
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     * @param User $user the user to restore
     *
     * @return \Illuminate\Http\JsonResponse the JSON response
     */
    public function restore(PublicAdministration $publicAdministration, User $user): JsonResponse
    {
        if (!$user->trashed()) {
            return response()->json(null, 304);
        }

        $user->publicAdministrations()->get()->map(function ($publicAdministration) use ($user) {
            Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
                $user->retract(UserRole::REMOVED);
            });
        });

        $user->restore();

        return response()->json(['result' => 'ok', 'id' => $user->uuid, 'status' => $user->status->description]);
    }

    /**
     * Get the users data.
     *
     * @param PublicAdministration|null $publicAdministration
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response the JSON format
     */
    public function dataJson(PublicAdministration $publicAdministration)
    {
        return Datatables::of(auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? $publicAdministration->users()->withTrashed()->get() : current_public_administration()->users)
            ->setTransformer(new UserTransformer())
            ->make(true);
    }

    /**
     * Get the user permissions on websites.
     *
     * @param User|null $user the user to initialize permissions or null for default
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response the JSON format
     */
    public function dataWebsitesPermissionsJson(User $user)
    {
        return Datatables::of(current_public_administration()->websites)
            ->setTransformer(new WebsitesPermissionsTransformer())
            ->make(true);
    }

    /**
     * Validate user isn't the last active admin.
     *
     * @param Validator $validator the validator
     */
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
