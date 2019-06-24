<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserInvited;
use App\Models\User;
use App\Transformers\SuperAdminTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class AdminUserController extends Controller
{
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.user.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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

        return redirect()->route('admin-dashboard')
            ->withMessages([
                ['success' => 'Il nuovo utente è stato invitato come amministratore al progetto Web Analytics Italia.'],
                ['info' => 'Comunica al nuovo utente la sua password temporanea ' . $temporaryPassword . ' usando un canale diverso dalla mail ' . $validatedData['email'] . '.'],
                ['warning' => 'Attenzione! Questa password non sarà più visualizzata.'],
            ]); //TODO: put message in lang file
    }

    /**
     * Show the profile page.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('pages.admin.user.show')->with(['user' => $user]);
    }

    /**
     * Show the profile edit form.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('pages.admin.user.edit')->with(['user' => $user]);
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

        logger()->info('User ' . auth()->user()->uuid . ' updated administrator ' . $user->uuid);

        return redirect()->route('admin-dashboard')->withMessage(['success' => "L'utente amministratore " . $user->getInfo() . ' è stato modificato.']); //TODO: put message in lang file
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

    public function dataJson()
    {
        return DataTables::of(User::whereIs(UserRole::SUPER_ADMIN))
            ->setTransformer(new SuperAdminTransformer())
            ->make(true);
    }
}
