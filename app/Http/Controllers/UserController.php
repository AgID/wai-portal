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
use App\Traits\HasRoleAwareUrls;
use App\Traits\SendsResponse;
use App\Transformers\UserTransformer;
use App\Transformers\WebsitesPermissionsTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
    use SendsResponse;
    use HasRoleAwareUrls;

    /**
     * Display the users list.
     *
     * @param PublicAdministration $publicAdministration the public administration the users belong to
     *
     * @return \Illuminate\View\View the view
     */
    public function index(PublicAdministration $publicAdministration): View
    {
        $usersDatatable = [
            'datatableOptions' => [
                'searching' => true,
                'columnFilters' => [
                    'status' => [
                        'filterLabel' => __('stato'),
                    ],
                ],
            ],
            'columns' => [
                ['data' => 'name', 'name' => 'nome e cognome'],
                ['data' => 'email', 'name' => 'email'],
                ['data' => 'added_at', 'name' => 'iscritto dal'],
                ['data' => 'status', 'name' => 'stato'],
                ['data' => 'icons', 'name' => '', 'orderable' => false],
                ['data' => 'buttons', 'name' => '', 'orderable' => false],
            ],
            'source' => $this->getRoleAwareUrl('users.data.json', [], $publicAdministration),
            'caption' => __('elenco degli utenti presenti su Web Analytics Italia'),
            'columnsOrder' => [['added_at', 'asc'], ['name', 'asc']],
        ];

        $userCreateUrl = $this->getRoleAwareUrl('users.create', [], $publicAdministration);

        return view('pages.users.index')->with(compact('userCreateUrl'))->with($usersDatatable);
    }

    /**
     * Show the form for creating a new user.
     *
     * @param PublicAdministration $publicAdministration the public administration the new user will belong to
     *
     * @return \Illuminate\View\View the view
     */
    public function create(PublicAdministration $publicAdministration): View
    {
        $websitesPermissionsDatatableSource = $this->getRoleAwareUrl('users.websites.permissions.data.json', [
            'user' => null,
            'oldPermissions' => old('permissions'),
        ], $publicAdministration);
        $userStoreUrl = $this->getRoleAwareUrl('users.store', [], $publicAdministration);
        $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource);

        return view('pages.users.add')->with(compact('userStoreUrl'))->with($websitesPermissionsDatatable);
    }

    /**
     * Create a new user.
     *
     * @param StoreUserRequest $request the incoming request
     * @param PublicAdministration $publicAdministration the public administration the user will belong to
     *
     * @throws \Exception if unable to generate user UUID
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function store(StoreUserRequest $request, PublicAdministration $publicAdministration): RedirectResponse
    {
        $authUser = auth()->user();
        $validatedData = $request->validated();
        $currentPublicAdministration = $authUser->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $user = User::create([
            'uuid' => Uuid::uuid4()->toString(),
            'fiscal_number' => $validatedData['fiscal_number'],
            'email' => $validatedData['email'],
            'status' => UserStatus::INVITED,
        ]);

        $user->publicAdministrations()->attach($currentPublicAdministration->id);
        $user->registerAnalyticsServiceAccount();

        $this->manageUserPermissions($validatedData, $currentPublicAdministration, $user);

        event(new UserInvited($user, $authUser, $currentPublicAdministration));

        $redirectUrl = $this->getRoleAwareUrl('users.index', [], $publicAdministration);

        return redirect()->to($redirectUrl)->withModal([
            'title' => __('Invito inoltrato'),
            'icon' => 'it-clock',
            'message' => __("Abbiamo appena inviato un invito all'indirizzo email <strong>:email</strong>.", ['email' => $user->email]),
            'image' => asset('images/invitation-email-sent.svg'),
        ]);
    }

    /**
     * Show the user details page.
     *
     * @param PublicAdministration|null $publicAdministration the public administration the user belongs to
     * @param User $user the user to display
     *
     * @return \Illuminate\View\View the view
     */
    public function show(PublicAdministration $publicAdministration, User $user): View
    {
        $websitesPermissionsDatatableSource = $this->getRoleAwareUrl('users.websites.permissions.data.json', [
            'user' => $user,
        ], $publicAdministration);
        $roleAwareUrls = $this->getRoleAwareUrlArray([
            'userEditUrl' => 'users.edit',
            'userVerificationResendUrl' => 'users.verification.resend',
            'userSuspendUrl' => 'users.suspend',
            'userReactivateUrl' => 'users.reactivate',
        ], [
            'user' => $user,
        ], $publicAdministration);
        $allRoles = $this->getAllRoles($user, $publicAdministration);

        $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource, true);

        return view('pages.users.show')->with(compact('user', 'allRoles'))->with($roleAwareUrls)->with($websitesPermissionsDatatable);
    }

    /**
     * Show the form to edit an existing user.
     *
     * @param Request $request the incoming request
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     * @param User $user the user to edit
     *
     * @return \Illuminate\View\View the view
     */
    public function edit(Request $request, PublicAdministration $publicAdministration, User $user): View
    {
        $oldPermissions = old('permissions', $request->session()->hasOldInput() ? [] : null);
        $websitesPermissionsDatatableSource = $this->getRoleAwareUrl('users.websites.permissions.data.json', [
            'user' => $user,
            'oldPermissions' => $oldPermissions,
        ], $publicAdministration);
        $userUpdateUrl = $this->getRoleAwareUrl('users.update', [
            'user' => $user,
        ], $publicAdministration);

        $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource);

        return view('pages.users.edit')->with(compact('user', 'userUpdateUrl'))->with($websitesPermissionsDatatable);
    }

    /**
     * Update the user information.
     *
     * @param UpdateUserRequest $request the incoming request
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
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
    public function update(UpdateUserRequest $request, PublicAdministration $publicAdministration, User $user): RedirectResponse
    {
        $validatedData = $request->validated();
        $currentPublicAdministration = auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        // Update user information
        if ($user->email !== $validatedData['email']) {
            // NOTE: the 'user update' event listener automatically
            //       sends a new email verification request and
            //       reset the email verification status
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
                if (!Str::contains($exception->getMessage(), 'unable to send mail')) {
                    throw $exception;
                }
            }
        }

        $this->manageUserPermissions($validatedData, $currentPublicAdministration, $user);

        $redirectUrl = $this->getRoleAwareUrl('users.index', [], $publicAdministration);

        return redirect()->to($redirectUrl)->withNotification([
            'title' => __('modifica sito web'),
            'message' => __("La modifica dell'utente è andata a buon fine.\nSe è stato modificato l'indirizzo email, l'utente riceverà un messaggio per effettuarne la verifica."),
            'status' => 'success',
            'icon' => 'it-check-circle',
        ]);
    }

    /**
     * Remove a user.
     * NOTE: Super admin only.
     *
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     * @param User $user the user to delete
     *
     * @throws \Exception if unable to delete
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function delete(PublicAdministration $publicAdministration, User $user)
    {
        try {
            if ($user->trashed()) {
                return $this->notModifiedResponse();
            }

            if ($user->status->is(UserStatus::PENDING)) {
                throw new OperationNotAllowedException('Pending users cannot be deleted.');
            }

            $validator = validator(request()->all())->after([$this, 'validateNotLastActiveAdministrator']);
            if ($validator->fails()) {
                throw new OperationNotAllowedException($validator->errors()->first('is_admin'));
            }

            $user->publicAdministrations()->get()->map(function ($publicAdministration) use ($user) {
                Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
                    $user->assign(UserRole::DELETED);
                });
            });

            // NOTE: don't use 'user->delete()' directly since
            //       it cascades delete to roles and permissions
            // See: https://github.com/JosephSilber/bouncer/issues/439
            $user->deleted_at = $user->freshTimestamp();
            $user->save();
        } catch (OperationNotAllowedException $exception) {
            report($exception);

            return $this->errorResponse($exception->getMessage(), $exception->getCode(), 400);
        }

        event(new UserDeleted($user));

        return $this->userResponse($user);
    }

    /**
     * Restore a soft-deleted user.
     * NOTE: Super admin only.
     *
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     * @param User $user the user to restore
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function restore(PublicAdministration $publicAdministration, User $user)
    {
        if (!$user->trashed()) {
            return $this->notModifiedResponse();
        }

        $user->publicAdministrations()->get()->map(function ($publicAdministration) use ($user) {
            Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
                $user->retract(UserRole::DELETED);
            });
        });

        $user->restore();

        return $this->userResponse($user);
    }

    /**
     * Suspend an existing user.
     *
     * @param Request $request the incoming request
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     * @param User $user the user to suspend
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function suspend(Request $request, PublicAdministration $publicAdministration, User $user)
    {
        if ($user->status->is(UserStatus::SUSPENDED)) {
            return $this->notModifiedResponse();
        }

        try {
            if ($user->is($request->user())) {
                throw new OperationNotAllowedException('Cannot suspend the current authenticated user.');
            }

            if ($user->status->is(UserStatus::PENDING)) {
                throw new InvalidUserStatusException('Pending users cannot be suspended.');
            }

            //NOTE: super admin are allowed to suspend the last active PA administrator
            if (auth()->user()->cannot(UserPermission::ACCESS_ADMIN_AREA)) {
                $validator = validator(request()->all())->after([$this, 'validateNotLastActiveAdministrator']);
                if ($validator->fails()) {
                    throw new OperationNotAllowedException($validator->errors()->first('is_admin'));
                }
            }

            $user->status = UserStatus::SUSPENDED;
            $user->save();

            return $this->userResponse($user);
        } catch (InvalidUserStatusException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'invalid operation for current user status';
            $httpStatusCode = 400;
        } catch (OperationNotAllowedException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'invalid operation for current user';
            $httpStatusCode = 403;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    /**
     * Reactivate an existing suspended user.
     *
     * @param PublicAdministration|null $publicAdministration the public administration the user belong to
     * @param User $user the user to reactivate
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function reactivate(?PublicAdministration $publicAdministration, User $user)
    {
        if (!$user->status->is(UserStatus::SUSPENDED)) {
            return $this->notModifiedResponse();
        }

        $user->status = $user->hasVerifiedEmail() ? UserStatus::ACTIVE : UserStatus::INVITED;
        $user->save();

        return $this->userResponse($user);
    }

    /**
     * Get the users data.
     *
     * @param Request $request the incoming request
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response the JSON format
     */
    public function dataJson(Request $request, PublicAdministration $publicAdministration)
    {
        return Datatables::of($request->user()->can(UserPermission::ACCESS_ADMIN_AREA)
                ? $publicAdministration->users()->withTrashed()->get()
                : optional(current_public_administration())->users ?? collect([]))
            ->setTransformer(new UserTransformer())
            ->make(true);
    }

    /**
     * Get the user permissions on websites.
     *
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     * @param User $user the user to initialize permissions or null for default
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response the JSON format
     */
    public function dataWebsitesPermissionsJson(PublicAdministration $publicAdministration, User $user)
    {
        return Datatables::of((auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)
                ? $publicAdministration
                : current_public_administration())
            ->websites)
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
        $publicAdministration = request()->route('publicAdministration', current_public_administration());
        $user = request()->route('user');

        if ($user->isTheLastActiveAdministratorOf($publicAdministration)) {
            $validator->errors()->add('is_admin', 'The last administrator cannot be removed or suspended.');
        }
    }

    /**
     * Return all roles in for the specified user.
     *
     * @param User $user the user
     * @param PublicAdministration $publicAdministration the public administration scope
     *
     * @return Collection the collection of all the user roles
     */
    public function getAllRoles(User $user, PublicAdministration $publicAdministration): Collection
    {
        $scope = $publicAdministration->id ?? Bouncer::scope()->get();

        return Bouncer::scope()->onceTo($scope, function () use ($user) {
            return $user->getAllRoles();
        });
    }

    /**
     * Get the datatable parameters for websites permission with specified source.
     *
     * @param string $source the source paramater for the websites permission datatable
     * @param bool|null $readonly wether the datatable is readonly
     *
     * @return array the datatable parameters
     */
    public function getDatatableWebsitesPermissionsParams(string $source, bool $readonly = false): array
    {
        return [
            'datatableOptions' => [
                'searching' => true,
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
                ['data' => 'status', 'name' => __('stato')],
                ['data' => ($readonly ? 'icons' : 'toggles'), 'name' => __('permessi sui dati analytics'), 'orderable' => false, 'searchable' => false],
            ],
            'source' => $source . ($readonly ? '?readOnly' : ''),
            'caption' => __('elenco dei siti web presenti su Web Analytics Italia'),
            'columnsOrder' => [['website_name', 'asc']],
        ];
    }

    /**
     * Manage websites permissions for a user.
     *
     * @param array $validatedData the permissions array
     * @param PublicAdministration $publicAdministration the public administration the website belongs to
     * @param User $user the user
     *
     * @throws BindingResolutionException if unable to bind to the service
     * @throws AnalyticsServiceException if unable to contact the Analytics Service
     * @throws CommandErrorException if command finishes with error
     * @throws TenantIdNotSetException if the tenant id is not set in the current session
     */
    private function manageUserPermissions(array $validatedData, PublicAdministration $publicAdministration, User $user): void
    {
        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($validatedData, $publicAdministration, $user) {
            $isAdmin = $validatedData['is_admin'] ?? false;
            $websitesPermissions = $validatedData['permissions'] ?? [];
            $publicAdministration->websites->map(function ($website) use ($user, $isAdmin, $websitesPermissions) {
                if ($isAdmin) {
                    $user->assign(UserRole::ADMIN);
                    $user->setWriteAccessForWebsite($website);

                    return $user;
                } else {
                    $user->assign(UserRole::DELEGATED);
                    if (empty($websitesPermissions[$website->id])) {
                        $user->setNoAccessForWebsite($website);

                        return $user;
                    }

                    if (in_array(UserPermission::MANAGE_ANALYTICS, $websitesPermissions[$website->id])) {
                        $user->setWriteAccessForWebsite($website);

                        return $user;
                    }

                    if (in_array(UserPermission::READ_ANALYTICS, $websitesPermissions[$website->id])) {
                        $user->setViewAccessForWebsite($website);

                        return $user;
                    }
                }
            })->map(function ($user) use ($publicAdministration) {
                if ($user->hasAnalyticsServiceAccount()) {
                    $user->syncWebsitesPermissionsToAnalyticsService($publicAdministration);
                }
            });
        });
    }
}
