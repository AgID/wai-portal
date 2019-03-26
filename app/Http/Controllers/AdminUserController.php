<?php

namespace App\Http\Controllers;

use App\Events\Auth\Invited;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
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
            'password' => Hash::make($temporaryPassword),
            'password_changed_at' => Carbon::now()->subDays(1 + config('auth.password_expiry')),
            'status' => 'invited',
        ]);

        $user->assign('super-admin');

        if (!empty($user->passwordResetToken)) {
            $user->passwordResetToken->delete();
        }

        event(new Invited($user, $request->user()));

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

        logger()->info('User ' . auth()->user()->getInfo() . ' updated administrator ' . $user->getInfo());

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
}
