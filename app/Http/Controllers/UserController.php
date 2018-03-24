<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Transformers\UserTransformer;
use App\Jobs\SendVerificationEmail;
use Illuminate\Http\Request;
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
            'caption' => 'Elenco degli utenti web abilitati su Web Analytics Italia' //TODO: set title in lang file
        ];

        return view('pages.users')->with($datatable);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.add_user');
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

        $user = User::create([
            'fiscalNumber' => $validatedData['fiscalNumber'],
            'email' => $validatedData['email'],
            'public_administration_id' => auth()->user()->publicAdministration->id,
            'status' => 'invited'
        ]);
        $user->verificationToken()->create([
            'token' => bin2hex(random_bytes(32))
        ]);
        $user->assign($validatedData['role']);
        dispatch(new SendVerificationEmail($user));

        logger()->info('User '.auth()->user()->getInfo().' added a new user ['.$validatedData['email'].'] as '.$validatedData['role'].' for "'.auth()->user()->publicAdministration->name.'"');

        return redirect(route('dashboard'))->withMessage(['success' => 'Il nuovo utente è stato invitato al progetto Web Analytics Italia']); //TODO: put message in lang file
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
