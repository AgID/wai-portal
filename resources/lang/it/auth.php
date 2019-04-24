<?php

use App\Enums\UserRole;
use App\Enums\UserPermission;

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
    'spid_failed' => "L'accesso con SPID è fallito.",
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
        UserRole::REGISTERED => [
            'description' => "Può proporre un nuovo sito web per una nuova pubblica amministrazione.",
        ],
        UserRole::ADMIN => [
            'description' => "Può gestire gli utenti, i siti e i dati analytics per la sua pubblica amministrazione.",
        ],
        UserRole::SUPER_ADMIN => [
            'description' => 'Può amministrare la piattaforma ' . config('app.name') . '.'
        ],
    ],
    'permissions' => [
        UserPermission::ACCESS_ADMIN_AREA => [
            'description' => "Può accedere all'area amministrativa della piattaforma.",
        ],
        UserPermission::MANAGE_USERS => [
            'description' => 'Può gestire gli utenti della propria PA.',
        ],
        UserPermission::MANAGE_WEBSITES => [
            'description' => 'Può gestire i siti web della propria PA.',
        ],
        UserPermission::MANAGE_ANALYTICS => [
            'description' => 'Può leggere e gestire i dati analytics di un sito della propria PA.',
        ],
        UserPermission::READ_ANALYTICS => [
            'description' => 'Può solo leggere i dati analytics di un sito della propria PA.',
        ],
        UserPermission::DO_NOTHING => [
            'description' => 'Non può fare nulla finché non effettua la registrazione della propria PA.',
        ],
    ],
    'password' => [
        'reset' => 'La password è stata reimpostata.',
        'changed' => 'La password è stata cambiata.'
    ]

];
