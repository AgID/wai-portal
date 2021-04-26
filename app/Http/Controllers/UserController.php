<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserDeleted;
use App\Events\User\UserEmailForPublicAdministrationChanged;
use App\Events\User\UserInvited;
use App\Events\User\UserReactivated;
use App\Events\User\UserSuspended;
use App\Events\User\UserUpdated;
use App\Exceptions\CommandErrorException;
use App\Exceptions\InvalidUserStatusException;
use App\Exceptions\OperationNotAllowedException;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Traits\HasRoleAwareUrls;
use App\Traits\SendsResponse;
use App\Transformers\UserApiTransformer;
use App\Transformers\UserTransformer;
use App\Transformers\WebsitesPermissionsTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Yajra\DataTables\DataTables;

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
     * @param Request $request the incoming request
     * @param PublicAdministration $publicAdministration the public administration the users belong to
     *
     * @return View|RedirectResponse
     */
    public function index(Request $request, PublicAdministration $publicAdministration)
    {
        $user = $request->user();
        if ($user->publicAdministrations->isEmpty() && $user->cannot(UserPermission::ACCESS_ADMIN_AREA)) {
            $request->session()->reflash();

            return redirect()->route('websites.index');
        }

        $usersDatatable = [
            'datatableOptions' => [
                'searching' => [
                    'label' => __('cerca tra gli utenti'),
                ],
                'columnFilters' => [
                    'status' => [
                        'filterLabel' => __('stato'),
                    ],
                ],
            ],
            'columns' => [
                ['data' => 'name', 'name' => __('nome e cognome')],
                ['data' => 'email', 'name' => __('email')],
                ['data' => 'added_at', 'name' => __('iscritto dal')],
                ['data' => 'status', 'name' => __('stato')],
                ['data' => 'icons', 'name' => '', 'orderable' => false],
                ['data' => 'buttons', 'name' => '', 'orderable' => false],
            ],
            'source' => $this->getRoleAwareUrl('users.data.json', [], $publicAdministration),
            'caption' => __('elenco degli utenti presenti su :app', ['app' => config('app.name')]),
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

    public function store(StoreUserRequest $request, PublicAdministration $publicAdministration): RedirectResponse
    {
        $data = $this->storeMethod($request, $publicAdministration, false);
        $redirectUrl = $data['redirectUri'];

        if (true === $data['error']) {
            return redirect()->to($redirectUrl)->withModal([
                'title' => __($data['title']),
                'icon' => 'it-clock',
                'message' => __($data['message']),
                'image' => asset('images/closed.svg'),
            ]);
        }

        return redirect()->to($redirectUrl)->withModal([
            'title' => __($data['title']),
            'icon' => 'it-clock',
            'message' => $data['message'],
            'image' => asset('images/invitation-email-sent.svg'),
        ]);
    }

    public function storeJson(StoreUserRequest $request)
    {
        $publicAdministration = get_public_administration_from_token();
        $data = $this->storeMethod($request, $publicAdministration, true);

        if (true === $data['error']) {
            return response()->json([
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $data['type'],
            ], 400);
        }

        $user = (new UserApiTransformer())->transform($data['user'], $publicAdministration);

        return response()
            ->json($user, 201)
            ->header('Location', $this->getUriUserAPI($data['user']->fiscal_number));
    }

    /**
     * Show the user details page.
     *
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     * @param User $user the user to display
     *
     * @return \Illuminate\View\View the view
     */
    public function show(PublicAdministration $publicAdministration, User $user): View
    {
        $publicAdministration = request()->route('publicAdministration', current_public_administration());
        $emailPublicAdministrationUser = $user->getEmailforPublicAdministration($publicAdministration);
        $statusPublicAdministrationUser = $user->getStatusforPublicAdministration($publicAdministration);

        $websitesPermissionsDatatableSource = $this->getRoleAwareUrl('users.websites.permissions.data.json', [
            'user' => $user,
        ], $publicAdministration);

        $publicAdministration = ($publicAdministration->id ?? false) ? $publicAdministration : current_public_administration();
        $publicAdministrationUser = $user->publicAdministrationsWithSuspended()->where('public_administration_id', $publicAdministration->id)->first();
        $userPublicAdministrationStatus = UserStatus::fromValue(intval($publicAdministrationUser->pivot->user_status));

        $roleAwareUrls = $this->getRoleAwareUrlArray([
            'userEditUrl' => 'users.edit',
            'userVerificationResendUrl' => 'users.verification.resend',
            'userSuspendUrl' => 'users.suspend',
            'userReactivateUrl' => 'users.reactivate',
            'userGenerateCredentials' => 'users.generate.credentials',
            'userSwagger' => 'show.swagger',
        ], [
            'user' => $user,
        ], $publicAdministration);
        $allRoles = $this->getAllRoles($user, $publicAdministration);

        $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource, true);

        return view('pages.users.show')
            ->with(compact('user', 'allRoles', 'emailPublicAdministrationUser', 'statusPublicAdministrationUser'))
            ->with($roleAwareUrls)
            ->with($websitesPermissionsDatatable);
    }

    public function showJson(Request $request): JsonResponse
    {
        $publicAdministration = get_public_administration_from_token();
        $user = $this->getUserFromFiscalNumber($request);

        $user = (new UserApiTransformer())->transform($user, $publicAdministration);

        if (!$user) {
            return response()->json([
                'error' => true,
                'message' => 'User Not Found',
            ], 404);
        }

        return response()->json($user, 200);
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
        $publicAdministration = request()->route('publicAdministration', current_public_administration());
        $emailPublicAdministrationUser = $user->getEmailforPublicAdministration($publicAdministration);

        $oldPermissions = old('permissions', $request->session()->hasOldInput() ? [] : null);
        $websitesPermissionsDatatableSource = $this->getRoleAwareUrl('users.websites.permissions.data.json', [
            'user' => $user,
            'oldPermissions' => $oldPermissions,
        ], $publicAdministration);
        $userUpdateUrl = $this->getRoleAwareUrl('users.update', [
            'user' => $user,
        ], $publicAdministration);

        $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource);

        if (auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)) {
            $isAdmin = Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
                return $user->isA(UserRole::ADMIN);
            });
        } else {
            $isAdmin = $user->isA(UserRole::ADMIN);
        }

        return view('pages.users.edit')->with(compact('user', 'userUpdateUrl', 'isAdmin', 'emailPublicAdministrationUser'))->with($websitesPermissionsDatatable);
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
        $updatedUser = $this->updateMethod($request, $publicAdministration, $user);

        $redirectUrl = $this->getRoleAwareUrl('users.index', [], $publicAdministration);

        return redirect()->to($redirectUrl)->withNotification([
            'title' => __('modifica utente'),
            'message' => __("La modifica dell'utente è andata a buon fine."),
            'status' => 'success',
            'icon' => 'it-check-circle',
        ]);
    }

    public function updateApi(Request $request, UpdateUserRequest $reqUpdate): JsonResponse
    {
        $user = $this->getUserFromFiscalNumber($request);
        $publicAdministration = get_public_administration_from_token();

        $updateUser = $this->updateMethod($reqUpdate, $publicAdministration, $user);

        $data = (new UserApiTransformer())->transform($updateUser, $publicAdministration);

        return response()->json($data, 200);
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
        $publicAdministrationUser = $user->publicAdministrations()->where('public_administration_id', $publicAdministration->id)->first();

        if (empty($publicAdministrationUser)) {
            return $this->notModifiedResponse();
        }

        $userPublicAdministrationStatus = $user->getStatusforPublicAdministration($publicAdministration);

        try {
            if ($userPublicAdministrationStatus->is(UserStatus::PENDING)) {
                throw new OperationNotAllowedException('Pending users cannot be deleted.');
            }

            $validator = validator(request()->all())->after([$this, 'validateNotLastActiveAdministrator']);
            if ($validator->fails()) {
                throw new OperationNotAllowedException($validator->errors()->first('is_admin'));
            }

            Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
                $user->disallow($user->abilities->pluck('id'));
                $user->retract($user->roles->pluck('name'));
            });

            $publicAdministration->users()->detach([$user->id]);
        } catch (OperationNotAllowedException $exception) {
            report($exception);

            return $this->errorResponse($exception->getMessage(), $exception->getCode(), 400);
        }

        event(new UserDeleted($user, $publicAdministration));

        return $this->userResponse($user, $publicAdministration);
    }

    public function deleteApi(Request $request): JsonResponse
    {
        $user = $this->getUserFromFiscalNumber($request);
        $publicAdministration = get_public_administration_from_token();
        $deleteUser = $this->delete($publicAdministration, $user);

        return response()->json($deleteUser, 200);
    }

    public function suspendApi(Request $request): JsonResponse
    {
        $user = $this->getUserFromFiscalNumber($request);
        $publicAdministration = get_public_administration_from_token();
        $response = $this->suspend($request, $publicAdministration, $user);

        if ($response instanceof JsonResponse) {
            return response()->json((new UserApiTransformer())->transform($user, $publicAdministration), 200);
        } elseif (property_exists('status', $response)) {
            response()->json(['error' => "The request can't be processed"], $response->status());
        } else {
            return response()->json(['error' => 'Bad request'], 400);
        }
    }

    public function reactivateApi(Request $request): JsonResponse
    {
        $user = $this->getUserFromFiscalNumber($request);
        $publicAdministration = get_public_administration_from_token();
        $response = $this->reactivate($publicAdministration, $user);

        if ($response instanceof JsonResponse) {
            return response()->json((new UserApiTransformer())->transform($user, $publicAdministration), 200);
        } elseif (property_exists('status', $response)) {
            response()->json(['error' => "The request can't be processed"], $response->status());
        } else {
            return response()->json(['error' => 'Bad request'], 400);
        }
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
        $publicAdministration = ($publicAdministration->id ?? false) ? $publicAdministration : current_public_administration();
        $userPublicAdministrationStatus = $user->getStatusforPublicAdministration($publicAdministration);

        if ($userPublicAdministrationStatus->is(UserStatus::SUSPENDED)) {
            return $this->notModifiedResponse();
        }

        try {
            if ($user->is($request->user())) {
                throw new OperationNotAllowedException('Cannot suspend the current authenticated user.');
            }

            if ($userPublicAdministrationStatus->is(UserStatus::PENDING)) {
                throw new InvalidUserStatusException('Pending users cannot be suspended.');
            }

            if ($userPublicAdministrationStatus->is(UserStatus::INVITED)) {
                throw new InvalidUserStatusException('Invited users cannot be suspended.');
            }

            //NOTE: super admin are allowed to suspend the last active administrator of a public administration
            if (null !== auth()->user() && auth()->user()->cannot(UserPermission::ACCESS_ADMIN_AREA)) {
                $validator = validator(request()->all())->after([$this, 'validateNotLastActiveAdministrator']);
                if ($validator->fails()) {
                    throw new OperationNotAllowedException($validator->errors()->first('is_admin'));
                }
            }

            $publicAdministration->users()->updateExistingPivot($user->id, ['user_status' => UserStatus::SUSPENDED]);

            event(new UserSuspended($user, $publicAdministration));
            event(new UserUpdated($user, $publicAdministration));

            return $this->userResponse($user, $publicAdministration);
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
     * @param PublicAdministration $publicAdministration the public administration the user belong to
     * @param User $user the user to reactivate
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function reactivate(PublicAdministration $publicAdministration, User $user)
    {
        $publicAdministration = ($publicAdministration->id ?? false) ? $publicAdministration : current_public_administration();
        $userPublicAdministrationStatus = $user->getStatusforPublicAdministration($publicAdministration);

        if (!$userPublicAdministrationStatus->is(UserStatus::SUSPENDED)) {
            return $this->notModifiedResponse();
        }

        $updatedStatus = $user->hasVerifiedEmail() ? UserStatus::ACTIVE : UserStatus::INVITED;
        $publicAdministration->users()->updateExistingPivot($user->id, ['user_status' => $updatedStatus]);

        event(new UserReactivated($user, $publicAdministration));
        event(new UserUpdated($user, $publicAdministration));

        return $this->userResponse($user, $publicAdministration);
    }

    /**
     * Get the users data.
     *
     * @param Request $request the incoming request
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response in JSON format
     */
    public function dataJson(Request $request, PublicAdministration $publicAdministration)
    {
        return DataTables::of($this->baseDataJson($request, $publicAdministration))
            ->setTransformer(new UserTransformer())
            ->make(true);
    }

    public function dataApiJson(): JsonResponse
    {
        $publicAdministration = get_public_administration_from_token();
        $users = $publicAdministration->users->map(function ($user) use ($publicAdministration) {
            return (new UserApiTransformer())->transform($user, $publicAdministration);
        });

        return response()->json($users, 200);
    }

    /**
     * Get the user permissions on websites.
     *
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     * @param User $user the user to initialize permissions or null for default
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response in JSON format
     */
    public function dataWebsitesPermissionsJson(PublicAdministration $publicAdministration, User $user)
    {
        return DataTables::of(auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration->websites
            : current_public_administration()->websites)
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
        if (null === $publicAdministration) {
            $publicAdministration = get_public_administration_from_token();
        }
        $publicAdministrationRoute = request()->route('publicAdministration', $publicAdministration);
        $user = request()->route('user');

        $user = ('api.users.delete' === request()->route()->getName())
            ? get_user_from_fiscalnumber()
            : request()->route('user');

        if ($user->isTheLastActiveAdministratorOf($publicAdministrationRoute)) {
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
     * @param bool $readonly wether the datatable is readonly
     *
     * @return array the datatable parameters
     */
    public function getDatatableWebsitesPermissionsParams(string $source, bool $readonly = false): array
    {
        return [
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
                ['data' => 'status', 'name' => __('stato')],
                ['data' => ($readonly ? 'icons' : 'toggles'), 'name' => __('permessi sui dati analytics'), 'orderable' => false, 'searchable' => false],
            ],
            'source' => $source . ($readonly ? '?readOnly' : ''),
            'caption' => __('elenco dei siti web presenti su :app', ['app' => config('app.name')]),
            'columnsOrder' => [['website_name', 'asc']],
        ];
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
    protected function storeMethod(StoreUserRequest $request, PublicAdministration $publicAdministration, bool $jsonResponse): array
    {
        $authUser = auth()->user();
        $validatedData = $request->validated();

        if (null !== $authUser) {
            $currentPublicAdministration = $authUser->can(UserPermission::ACCESS_ADMIN_AREA)
                ? $publicAdministration
                : current_public_administration();
        } else {
            $currentPublicAdministration = get_public_administration_from_token();
        }

        $redirectUrl = isset($authUser) ? $this->getRoleAwareUrl('users.index', [], $publicAdministration) : null;
        // If existingUser is filled the user is already in the database
        if (isset($validatedData['existingUser']) && isset($validatedData['existingUser']->email)) {
            $user = $validatedData['existingUser'];
            $userInCurrentPublicAdministration = $user->publicAdministrationsWithSuspended->where('id', $currentPublicAdministration->id)->isNotEmpty();

            if ($userInCurrentPublicAdministration) {
                return [
                    'error' => true,
                    'type' => 'user.exist.in.amministration',
                    'title' => "Non è possibile inoltrare l'invito",
                    'message' => "L'utente fa già parte di questa pubblica amministrazione",
                    'redirectUri' => $redirectUrl,
                ];
            }

            $user_message = implode("\n", [
                __("Abbiamo inviato un invito all'indirizzo email :email.", ['email' => '<strong>' . e($validatedData['email']) . '</strong>']),
                "\n" . __("L'invito potrà essere confermato dall'utente al prossimo accesso."),
            ]);
        } else {
            $user = User::create([
                'uuid' => Uuid::uuid4()->toString(),
                'fiscal_number' => $validatedData['fiscal_number'],
                'email' => $validatedData['email'],
                'status' => UserStatus::INVITED,
            ]);

            $user_message = true === $jsonResponse
                ? 'test'
                : implode("\n", [
                    __("Abbiamo inviato un invito all'indirizzo email :email.", ['email' => '<strong>' . e($validatedData['email']) . '</strong>']),
                    "\n" . __("L'invito scade dopo :expire giorni e può essere rinnovato.", ['expire' => config('auth.verification.expire')]),
                    "\n<strong>" . __("Attenzione! Se dopo :purge giorni l'utente non avrà ancora accettato l'invito, sarà rimosso.", ['purge' => config('auth.verification.purge')]) . '</strong>',
                ]);
        }

        if (!$user->hasAnalyticsServiceAccount()) {
            $user->registerAnalyticsServiceAccount();
        }

        $user->publicAdministrations()->attach(
            $currentPublicAdministration->id,
            ['user_email' => $validatedData['email'], 'user_status' => UserStatus::INVITED]
        );

        $this->manageUserPermissions($validatedData, $currentPublicAdministration, $user);

        isset($authUser) ? event(new UserInvited($user, $authUser, $currentPublicAdministration)) : null;

        return [
            'error' => false,
            'type' => 'user.creation.success',
            'title' => 'Invito inoltrato',
            'message' => $user_message,
            'redirectUri' => $redirectUrl,
            'user' => $user,
        ];
    }

    protected function getUserFromFiscalNumber(Request $request): User
    {
        /* Codice Fiscale */
        $fn = $request->fn;
        /* Trova l'utente col codice fiscale */
        $user = User::findNotSuperAdminByFiscalNumber($fn);

        return $user;
    }

    protected function updateMethod(UpdateUserRequest $request, PublicAdministration $publicAdministration, User $user)
    {
        $validatedData = $request->validated();
        $authUser = auth()->user();
        if (isset($authUser)) {
            $currentPublicAdministration = auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)
                ? $publicAdministration
                : current_public_administration();
        } else {
            $currentPublicAdministration = get_public_administration_from_token();
        }
        // Update user information
        if ($user->email !== $validatedData['email']) {
            // NOTE: the 'user update' event listener automatically
            //       sends a new email verification request and
            //       reset the email verification status
            $user->email = $validatedData['email'];

            // Update Analytics Service account if needed
            // NOTE: at this point, user must have an analytics account
            $user->updateAnalyticsServiceAccountEmail();
        }

        $emailPublicAdministrationUser = $user->getEmailforPublicAdministration($currentPublicAdministration);

        if ($user->status->is(UserStatus::INVITED) && array_key_exists('fiscal_number', $validatedData)) {
            $user->fiscal_number = $validatedData['fiscal_number'];
        }

        if ($user->isDirty()) {
            $user->save();
        }

        $this->manageUserPermissions($validatedData, $currentPublicAdministration, $user);

        if ($emailPublicAdministrationUser !== $validatedData['emailPublicAdministrationUser']) {
            $user->publicAdministrations()->updateExistingPivot($currentPublicAdministration->id, ['user_email' => $validatedData['emailPublicAdministrationUser']]);
            event(new UserEmailForPublicAdministrationChanged($user, $currentPublicAdministration, $validatedData['emailPublicAdministrationUser']));
        }

        return $user;
    }

    protected function baseDataJson(Request $request, PublicAdministration $publicAdministration)
    {
        return $request->user()->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration->users()->withTrashed()->get()
            : optional(current_public_administration())->users ?? collect([]);
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
                    $user->retract(UserRole::DELEGATED);
                    $user->assign(UserRole::ADMIN);
                    $user->setWriteAccessForWebsite($website);

                    return $user;
                } else {
                    $user->retract(UserRole::ADMIN);
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

    private function getUriUserAPI(string $fn): string
    {
        return config('kong-service.api_url') .
            str_replace('/api/', '/portal/', route('api.users.show', ['fn' => $fn], false));
    }
}
