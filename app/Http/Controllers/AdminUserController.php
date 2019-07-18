<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserInvited;
use App\Exceptions\OperationNotAllowedException;
use App\Models\User;
use App\Transformers\SuperAdminTransformer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

/**
 * Super-admin users management controller.
 */
class AdminUserController extends Controller
{
    /**
     * Display super-admin user list.
     *
     * @return View the view
     */
    public function index(): View
    {
        $data = [
            'columns' => [
                ['data' => 'name', 'name' => 'Cognome e nome'],
                ['data' => 'email', 'name' => 'Email'],
                ['data' => 'added_at', 'name' => 'Iscritto dal'],
                ['data' => 'status', 'name' => 'Stato'],
                ['data' => 'buttons', 'name' => 'Azioni'],
            ],
            'source' => route('admin.users.data.json'),
            'caption' => 'Elenco dei super amministratori del Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc'], ['name', 'asc']],
        ];

        return view('pages.admin.user.index')->with($data);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View the view
     */
    public function create(): View
    {
        return view('pages.admin.user.add');
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
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'familyName' => 'required',
            'email' => 'required|unique:users|email',
        ]);

        $temporaryPassword = Str::random(16);

        $user = User::create([
            'name' => $validatedData['name'],
            'familyName' => $validatedData['familyName'],
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
            ->withMessages([
                ['success' => 'Il nuovo utente è stato invitato come amministratore al progetto Web Analytics Italia.'],
                ['info' => 'Comunica al nuovo utente la sua password temporanea ' . $temporaryPassword . ' usando un canale diverso dalla mail ' . $validatedData['email'] . '.'],
                ['warning' => 'Attenzione! Questa password non sarà più visualizzata.'],
            ]); //TODO: put message in lang file
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
        return view('pages.admin.user.show')->with(['user' => $user]);
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
        return view('pages.admin.user.edit')->with(['user' => $user]);
    }

    /**
     * Update the user information.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     * @param User $user the user to update
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'familyName' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->ignore($user->id),
                'email',
            ],
        ]);

        $user->fill([
            'name' => $validatedData['name'],
            'familyName' => $validatedData['familyName'],
            'email' => $validatedData['email'],
        ]);
        $user->save();

        return redirect()->route('admin.users.index')->withMessage(['success' => "L'utente amministratore " . $user->getInfo() . ' è stato modificato.']); //TODO: put message in lang file
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

            $validator = validator(request()->all())->after([$this, 'validateNotLastActiveAdministrator']);
            if ($validator->fails()) {
                throw new OperationNotAllowedException($validator->errors()->first('isAdmin'));
            }

            $user->status = UserStatus::SUSPENDED;
            $user->save();

            return response()->json(['result' => 'ok', 'id' => $user->uuid, 'status' => $user->status->description]);
        } catch (OperationNotAllowedException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid operation for current user';
            $httpStatusCode = 400;
        }

        return response()->json(['result' => 'error', 'message' => $message, 'code' => $code], $httpStatusCode);
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
            return response()->json(null, 304);
        }

        $user->status = $user->hasVerifiedEmail() ? UserStatus::ACTIVE : UserStatus::INVITED;
        $user->save();

        return response()->json(['result' => 'ok', 'id' => $user->uuid, 'status' => $user->status->description]);
    }

    /**
     * Get the super-admin users data.
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response the JSON format
     */
    public function dataJson()
    {
        return DataTables::of(User::whereIs(UserRole::SUPER_ADMIN))
            ->setTransformer(new SuperAdminTransformer())
            ->make(true);
    }

    /**
     * Validate user isn't the last active super-admin.
     *
     * @param Validator $validator the validator
     */
    public function validateNotLastActiveAdministrator(Validator $validator): void
    {
        $user = request()->route('user');
        if ($user->status->is(UserStatus::ACTIVE) && 1 === User::where('status', UserStatus::ACTIVE)->whereIs(UserRole::SUPER_ADMIN)->count()) {
            $validator->errors()->add('isAdmin', "Impossibile rimuovere l'utente " . $user->getInfo() . ' in quanto unico amministratore attivo del portale');
        }
    }
}
