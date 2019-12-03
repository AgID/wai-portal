<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserInvited;
use App\Events\User\UserReactivated;
use App\Events\User\UserSuspended;
use App\Exceptions\OperationNotAllowedException;
use App\Models\User;
use App\Traits\SendsResponse;
use App\Transformers\SuperAdminUserTransformer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

/**
 * Super admin users management controller.
 */
class SuperAdminUserController extends Controller
{
    use SendsResponse;

    /**
     * Display super admin user list.
     *
     * @return View the view
     */
    public function index(): View
    {
        $superUsersDatatable = [
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
            'source' => route('admin.users.data.json'),
            'caption' => __('elenco degli utenti super amministratori presenti su :app', ['app' => config('app.name')]),
            'columnsOrder' => [['added_at', 'asc'], ['name', 'asc']],
        ];

        return view('pages.admin.users.index')->with($superUsersDatatable);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return View the view
     */
    public function create(): View
    {
        return view('pages.admin.users.add');
    }

    /**
     * Create a new user.
     *
     * @param Request $request the incoming request
     *
     * @throws \Exception if unable to generate user UUID
     *
     * @return RedirectResponse the server redirect response
     */
    public function store(Request $request): RedirectResponse
    {
        $input = $request->all();
        $validatedData = validator($input, [
            'name' => 'required',
            'family_name' => 'required',
            'email' => 'required|email',
        ])->after(function ($validator) use ($input) {
            if (array_key_exists('email', $input) && User::where('email', $input['email'])
                ->whereIs(UserRole::SUPER_ADMIN)->get()->isNotEmpty()) {
                $validator->errors()->add('email', __('validation.unique', ['attribute' => __('validation.attributes.email')]));
            }
        })->validate();

        $temporaryPassword = Str::random(16);

        $user = User::create([
            'name' => $validatedData['name'],
            'family_name' => $validatedData['family_name'],
            'email' => $validatedData['email'],
            'uuid' => Uuid::uuid4()->toString(),
            'password' => Hash::make($temporaryPassword),
            'password_changed_at' => Carbon::now()->subDays(1 + config('auth.password_expiry')),
            'status' => UserStatus::INVITED,
        ]);

        $user->assign(UserRole::SUPER_ADMIN);

        if (!empty($user->passwordResetToken)) {
            $user->passwordResetToken->delete();
        }

        event(new UserInvited($user, $request->user()));

        return redirect()->route('admin.users.index')
            ->withModal([
                'title' => __('Nuovo utente super amministratore creato'),
                'icon' => 'it-check-circle',
                'message' => implode("\n", [
                    __(':user è stato aggiunto come amministratore di :app.', ['user' => '<strong>' . e($user->full_name) . '</strong>', 'app' => config('app.name')]),
                    __('Comunica al nuovo utente la sua password temporanea :password usando un canale diverso dalla mail :email.', ['password' => '<code>' . $temporaryPassword . '</code>', 'email' => $input['email']]),
                    "\n<strong>" . __('Attenzione! Questa password non sarà mai più visualizzata.') . '</strong>',
                ]),
                'image' => asset('images/invitation-email-sent.svg'),
            ]);
    }

    /**
     * Show the user details page.
     *
     * @param User $user the user to display
     *
     * @return View the view
     */
    public function show(User $user): View
    {
        return view('pages.admin.users.show')->with(['user' => $user]);
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
        return view('pages.admin.users.edit')->with(['user' => $user]);
    }

    /**
     * Update the user information.
     *
     * @param Request $request the incoming request
     * @param User $user the user to update
     *
     * @return RedirectResponse the server redirect response
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $input = $request->all();
        $validatedData = validator($input, [
            'name' => 'required',
            'family_name' => 'required',
            'email' => 'required|email',
        ])->after(function ($validator) use ($input, $user) {
            if (array_key_exists('email', $input) && User::where('email', $input['email'])
                ->where('id', '<>', $user->id)->whereIs(UserRole::SUPER_ADMIN)->get()->isNotEmpty()) {
                $validator->errors()->add('email', __('validation.unique', ['attribute' => __('validation.attributes.email')]));
            }
        })->validate();

        $user->fill([
            'name' => $validatedData['name'],
            'family_name' => $validatedData['family_name'],
            'email' => $validatedData['email'],
        ]);
        $user->save();

        return redirect()->route('admin.users.index')->withNotification([
            'title' => __('modifica utente'),
            'message' => __("L'utente amministratore :user è stato modificato.", ['user' => '<strong>' . e($user->info) . '</strong>']),
            'status' => 'success',
            'icon' => 'it-check-circle',
        ]);
    }

    /**
     * Suspend an existing user.
     *
     * @param Request $request the incoming request
     * @param User $user the user to suspend
     *
     * @return \Illuminate\Http\JsonResponse the JSON response
     */
    public function suspend(Request $request, User $user): JsonResponse
    {
        if ($user->status->is(UserStatus::SUSPENDED)) {
            return $this->notModifiedResponse();
        }

        try {
            if ($user->is($request->user())) {
                throw new OperationNotAllowedException('Cannot suspend the current authenticated user.');
            }

            $validator = validator(request()->all())->after([$this, 'validateNotLastActiveAdministrator']);
            if ($validator->fails()) {
                throw new OperationNotAllowedException($validator->errors()->first('last_admin'));
            }

            $user->status = UserStatus::SUSPENDED;
            $user->save();

            event(new UserSuspended($user));

            return $this->userResponse($user);
        } catch (OperationNotAllowedException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current user';
            $httpStatusCode = 400;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    /**
     * Reactivate an existing user.
     *
     * @param User $user the user to reactivate
     *
     * @return \Illuminate\Http\JsonResponse the JSON response
     */
    public function reactivate(User $user): JsonResponse
    {
        if (!$user->status->is(UserStatus::SUSPENDED)) {
            return $this->notModifiedResponse();
        }

        $user->status = $user->hasVerifiedEmail() ? UserStatus::ACTIVE : UserStatus::INVITED;
        $user->save();

        event(new UserReactivated($user));

        return $this->userResponse($user);
    }

    /**
     * Get the super admin users data.
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response the JSON format
     */
    public function dataJson()
    {
        return DataTables::of(User::whereIs(UserRole::SUPER_ADMIN))
            ->setTransformer(new SuperAdminUserTransformer())
            ->make(true);
    }

    /**
     * Validate user isn't the last active super admin.
     *
     * @param Validator $validator the validator
     */
    public function validateNotLastActiveAdministrator(Validator $validator): void
    {
        if (request()->route('user')->isTheLastActiveSuperAdministrator()) {
            $validator->errors()->add('last_admin', 'The last super administrator cannot be suspended.');
        }
    }
}
