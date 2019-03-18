<?php

namespace App\Http\Controllers;

use App\Jobs\SendVerificationEmail;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;

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

        $user = User::create([
            'name' => $validatedData['name'],
            'familyName' => $validatedData['familyName'],
            'email' => $validatedData['email'],
            'status' => 'invited',
        ]);

        $user->assign('super-admin');

        if (!empty($user->passwordResetToken)) {
            $user->passwordResetToken->delete();
        }

        $token = hash_hmac('sha256', Str::random(40), config('app.key'));
        $user->verificationToken()->create([
            'token' => Hash::make($token),
        ]);

        dispatch(new SendVerificationEmail($user, $token));

        logger()->info('User ' . auth()->user()->getInfo() . ' added a new user [' . $validatedData['email'] . '] as super-admin.');

        return redirect()->route('admin-dashboard')->withMessage(['success' => 'Il nuovo utente è stato invitato come amministratore al progetto Web Analytics Italia.']); //TODO: put message in lang file
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
        return Datatables::of(auth()->user()->publicAdministration->users)
            ->setTransformer(new UserTransformer())
            ->make(true);
    }
}
