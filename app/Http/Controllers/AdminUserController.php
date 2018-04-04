<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Transformers\UserTransformer;
use App\Jobs\SendVerificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class AdminUserController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.admin.add_user');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'familyName' => 'required',
            'email' => 'required|unique:users|email'
        ]);

        $user =  User::create([
            'name' => $validatedData['name'],
            'familyName' => $validatedData['familyName'],
            'email' => $validatedData['email'],
            'status' => 'invited'
        ]);

        $user->assign('super-admin');

        if (!empty($user->passwordResetToken)) {
            $user->passwordResetToken->delete();
        }

        $token = hash_hmac('sha256', str_random(40), config('app.key'));
        $user->verificationToken()->create([
            'token' => Hash::make($token)
        ]);

        dispatch(new SendVerificationEmail($user, $token));

        logger()->info('User '.auth()->user()->getInfo().' added a new user ['.$validatedData['email'].'] as super-admin.');

        return redirect(route('admin-dashboard'))->withMessage(['success' => 'Il nuovo utente è stato invitato come amministratore al progetto Web Analytics Italia.']); //TODO: put message in lang file
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Get all websites of the specified Public Administration
     * in JSON format (to be consumed by Datatables)
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function dataJson()
    {
        return Datatables::of(auth()->user()->publicAdministration->users)
            ->setTransformer(new UserTransformer)
            ->make(true);
    }
}
