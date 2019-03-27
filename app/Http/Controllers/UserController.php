<?php

namespace App\Http\Controllers;

use App\Events\Auth\Invited;
use App\Models\User;
use App\Transformers\UserTransformer;
use CodiceFiscale\Checker as FiscalNumberChecker;
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
                'actions' => 'Azioni',
            ],
            'source' => route('users-data-json'),
            'caption' => 'Elenco degli utenti web abilitati su Web Analytics Italia', //TODO: set title in lang file
            'columnsOrder' => [['added_at', 'asc'], ['name', 'asc']],
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users',
            'fiscalNumber' => [
                'required',
                'unique:users',
                function ($attribute, $value, $fail) {
                    $chk = new FiscalNumberChecker();
                    if (!$chk->isFormallyCorrect($value)) {
                        return $fail('Il codice fiscale non è formalmente valido.');
                    }
                },
            ],
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'fiscalNumber' => $validatedData['fiscalNumber'],
            'email' => $validatedData['email'],
            'status' => 'invited',
        ]);
        $user->publicAdministrations()->attach(session('tenant_id'));
        $user->assign($validatedData['role']);

        event(new Invited($user, current_public_administration(), $request->user()));

        logger()->info('User ' . auth()->user()->getInfo() . ' added a new user [' . $validatedData['email'] . '] as ' . $validatedData['role'] . ' for "' . current_public_administration()->name . '"');

        return redirect()->route('users-index')->withMessage(['success' => 'Il nuovo utente è stato invitato al progetto Web Analytics Italia']); //TODO: put message in lang file
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
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
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('pages.users.edit')->with(['user' => $user]);
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
        $validator = validator($request->all(), [
            'role' => 'required|exists:roles,name',
        ]);

        $validator->after(function ($validator) use ($user, $request) {
            $lastAdministrator = 1 == current_public_administration()->users()->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->count();
            if ('admin' == $user->roles()->first()->name && 'admin' != $request->input('role') && $lastAdministrator) {
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

        logger()->info('User ' . auth()->user()->getInfo() . ' updated user ' . $user->getInfo());

        return redirect()->route('users-index')->withMessage(['success' => "L'utente " . $user->getInfo() . ' è stato modificato.']); //TODO: put message in lang file
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
        return Datatables::of(current_public_administration()->users)
                ->setTransformer(new UserTransformer())
                ->make(true);
    }
}
