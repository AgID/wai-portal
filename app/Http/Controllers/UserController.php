<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Transformers\UserTransformer;
use App\Jobs\SendVerificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $datatable = [
            'columns' => [
                'name' => 'Nome',
                'familyName' => 'Cognome',
                'email' => 'Email',
                'role' => 'Ruolo',
                'added_at' => 'Iscritto dal',
                'status' => 'Stato',
                'actions' => 'Azioni'
            ],
            'source' => route('users-data-json'),
            'caption' => 'Elenco degli utenti web abilitati su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc'], ['name', 'asc']]
        ];

        return view('pages.users.index')->with($datatable);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.users.add');
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
            'email' => 'required|email|unique:users',
            'fiscalNumber' => [
                'required',
                'unique:users',
                'regex:/^(?:(?:[B-DF-HJ-NP-TV-Z]|[AEIOU])[AEIOU][AEIOUX]|[B-DF-HJ-NP-TV-Z]{2}[A-Z]){2}[\dLMNP-V]{2}(?:[A-EHLMPR-T](?:[04LQ][1-9MNP-V]|[1256LMRS][\dLMNP-V])|[DHPS][37PT][0L]|[ACELMRT][37PT][01LM])(?:[A-MZ][1-9MNP-V][\dLMNP-V]{2}|[A-M][0L](?:[1-9MNP-V][\dLMNP-V]|[0L][1-9MNP-V]))[A-Z]$/'
            ],
            'role' => 'required|exists:roles,name',
        ]);

        $user = new User;
        $user->fill([
            'fiscalNumber' => $validatedData['fiscalNumber'],
            'email' => $validatedData['email'],
            'status' => 'invited'
        ]);
        $user->publicAdministration()->associate(auth()->user()->publicAdministration);
        $user->save();

        $token = hash_hmac('sha256', str_random(40), config('app.key'));
        $user->verificationToken()->create([
            'token' => Hash::make($token)
        ]);

        $user->assign($validatedData['role']);

        dispatch(new SendVerificationEmail($user, $token));

        logger()->info('User '.auth()->user()->getInfo().' added a new user ['.$validatedData['email'].'] as '.$validatedData['role'].' for "'.auth()->user()->publicAdministration->name.'"');

        return redirect()->route('users-index')->withMessage(['success' => 'Il nuovo utente è stato invitato al progetto Web Analytics Italia']); //TODO: put message in lang file
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('pages.users.edit')->with(['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = validator($request->all(), [
            'role' => 'required|exists:roles,name'
        ]);

        $validator->after(function ($validator) use ($user, $request) {
            $publicAdministration = $user->publicAdministration;
            $lastAdministrator = $publicAdministration->users()->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->count() == 1;
            if ($user->roles()->first()->name == 'admin' && $request->input('role') != 'admin' && $lastAdministrator) {
                $validator->errors()->add('role', 'Deve restare almeno un utente amministratore per ogni PA.'); //TODO: put error message in lang file
            }
        });

        if ($validator->fails()) {
            return redirect()->route('users-edit', ['user' => $user])
                ->withErrors($validator)
                ->withInput();
        }

        $user->save();

        $user->assign($request->input('role'));

        logger()->info('User '.auth()->user()->getInfo().' updated user ' . $user->getInfo());

        return redirect()->route('users-index')->withMessage(['success' => "L'utente ". $user->getInfo() ." è stato modificato."]); //TODO: put message in lang file
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
