<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Le credenziali inserite non sono corrette.',
    'throttle' => 'Troppi tentativi di login. È possibile riprovare fra :seconds secondi.',
    'status' => [
        'info' => "Lo stato dell'utenza è:",
        'invited' => 'invitato',
        'inactive' => 'inattivo',
        'inactive_description' => "La verifica dell'indirizzo email non è stata ancora effettuata.",
        'pending' => 'in attesa',
        'pending_description' => "L'utenza non è ancora associata a nessuna PA.",
        'active' => 'attivo',
        'active_description' => "L'utenza è attiva.",
        'suspended' => 'sospeso',
        'suspended_description' => "L'utenza è sospesa."
    ],
    'roles' => [
        'info' => "Il ruolo dell'utente è:",
        'registered' => 'registrato',
        'registered_description' => "Può proporre un nuovo sito web per una nuova pubblica amministrazione.",
        'reader' => 'lettore',
        'reader_description' => "Può leggere i dati analytics per i siti web della sua pubblica amministrazione.",
        'manager' => 'gestore',
        'manager_description' => "Può leggere e gestire i dati analytics per i siti web della sua pubblica amministrazione.",
        'admin' => 'amministratore',
        'admin_description' => "Può gestire gli utenti, i siti e i dati analytics per la sua pubblica amministrazione.",
        'super-admin' => 'amministratore della piattaforma',
        'super-admin_description' => 'Può amministrare la piattaforma ' . config('app.name') . '.'
    ],
    'password' => [
        'reset' => 'La password è stata reimpostata.',
        'changed' => 'La password è stata cambiata.'
    ]

];
