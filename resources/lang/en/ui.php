<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for various
    | messages that we need to display to the user.
    |
    */

    'owner_short' => 'AGID',
    'owner_full' => "Agenzia per l'Italia Digitale",
    'partner_short' => 'Team Digitale',
    'partner_full' => 'Team per la Trasformazione Digitale',
    'site_title' => 'Web Analytics Italia',
    'site_subtitle' => 'I dati dei siti web della PA',
    'header_link_trasformazione' => 'Piano Triennale',
    'header_link_developers' => 'Developers',
    'header_link_design' => 'Designers',
    'header_link_forum' => 'Forum',
    'header_link_docs' => 'Docs',
    'header_link_github' => 'GitHub',
    'footer_link_privacy' => 'Privacy',
    'footer_link_legal_notes' => 'Note legali',
    'toggle_menu_label' => 'accedi al menu',
    'navigation_menu_label' => 'menu di navigazione',
    'navigation_network_menu_label' => 'menu di navigazione del network',
    'navigation_menu_esc' => 'esci dalla navigazione',
    'in_collaboration_with' => 'in collaborazione con',
    'follow_us' => 'seguici su',
    'version' => 'versione',
    'skiplink_goto_content' => 'vai ai contenuti',
    'skiplink_goto_navigation' => 'vai alla navigazione del sito',
    'scrolltop_label' => 'torna all\'inizio dei contenuti',
    'cookiebar_msg' => 'Questo sito utilizza cookie tecnici, analytics e di terze parti. Proseguendo nella navigazione accetti l’utilizzo dei cookie.',
    'cookiebar_accept' => 'Accetto',
    'cookiebar_privacy_policy' => 'Privacy policy',
    'session_expired' => 'La sessione è scaduta per inattività.',
    'return_home' => 'Ritorna alla pagina iniziale.',

    'pages' => [
        'home' => [
            'title' => 'Home'
        ],
        'faq' => [
            'title' => 'Domande frequenti'
        ],
        'privacy' => [
            'title' => 'Privacy'
        ],
        'legal-notes' => [
            'title' => 'Note legali'
        ],
        'dashboard' => [
            'title' => 'Dashboard',
            'websites' => 'Siti web',
            'users' => 'Utenti'
        ],
        'websites' => [
            'index' => [
                'title' => 'Tracked websites',
                'view_javascript_snippet' => 'show JS snippet',
                'go_to_analytics_service' => 'go to analytics',
                'show_website' => 'show website',
                'add_website' => 'add website',
                'edit_website' => 'edit website',
                'check_tracking' => 'check activation',
                'archive' => 'archive',
                'enable' => 'un-archive',
            ],
            'add-primary' => [
                'title' => 'Institutional website',
            ],
            'add' => [
                'title' => 'New website',
            ],
            'show' => [
                'title' => 'Show website',
            ],
            'edit' => [
                'title' => 'Modifica sito'
            ],
            'javascript-snippet' => [
                'title' => 'Snippet di codice Javascript'
            ]
        ],
        'users' => [
            'index' => [
                'title' => 'Users',
                'show_user' => 'show user',
                'add_user' => 'add user',
                'edit_user' => 'edit user',
                'suspend_user' => 'suspend user',
                'reactivate_user' => 'reactivate user',
                'restore_user' => 'restore user',
                'delete_user' => 'delete user',
            ],
            'show' => [
                'title' => 'Show user',
            ],
            'add' => [
                'title' => 'New user',
            ],
            'edit' => [
                'title' => 'Edit user',
            ],
        ],
        'spid-auth_login' => [
            'title' => 'Accesso con SPID'
        ],
        'profile' => [
            'show' => [
                'title' => 'User profile',
            ],
            'edit' => [
                'title' => 'User profile',
            ],
        ],
        'auth' => [
            'register' => [
                'title' => 'Register',
            ],
        ],
        'auth-verify' => [
            'title' => 'Verifica indirizzo email'
        ],
        'logs' => [
            'title' => 'Log visualization',
            'form' => [
                'legend' => 'Log filters',
                'inputs' => [
                    'start_date' => [
                        'label' => 'Starting date (gg/mm/aaaa)',
                    ],
                    'start_time' => [
                        'label' => 'Starting time (HH:mm)',
                    ],
                    'end_date' => [
                        'label' => 'Ending date (gg/mm/aaaa)',
                    ],
                    'end_time' => [
                        'label' => 'Ending time (HH:mm)',
                    ],
                    'message' => [
                        'label' => 'Message',
                    ],
                    'severity' => [
                        'label' => 'Minimum severity',
                    ],
                    'event' => [
                        'label' => 'Event',
                        'empty-selection' => 'Any',
                    ],
                    'exception' => [
                        'label' => 'Error',
                        'empty-selection' => 'Any',
                    ],
                    'job' => [
                        'label' => 'Task',
                        'empty-selection' => 'Any',
                    ],
                    'pa' => [
                        'label' => 'Public Administrations',
                    ],
                    'website' => [
                        'label' => 'Websites',
                    ],
                    'user' => [
                        'label' => 'Users',
                    ],
                ],
                'submit' => 'Show',
            ],
            'table' => [
                'headers' => [
                    'time' => 'Date and time',
                    'message' => 'Message',
                    'level' => 'Severity',
                    'trace' => 'Stack Trace',
                ],
                'caption' => 'Log messages for Web Analytics Italia portal',
            ],
        ],
        'admin-login' => [
            'title' => 'Accesso amministratori'
        ],
        'admin' => [
            'dashboard' => [
                'title' => 'Management Dashboard',
            ],
            'users' => [
                'index' => [
                    'title' => 'Administrators',
                    'show_user' => 'show administrator',
                    'add_user' => 'add administrator',
                    'edit_user' => 'edit administrator',
                    'suspend_user' => 'suspend administrator',
                    'reactivate_user' => 'reactivate administrator',
                ],
                'show' => [
                    'title' => 'Show administrator',
                ],
                'add' => [
                    'title' => 'Add administrator',
                ],
                'edit' => [
                    'title' => 'Edit administrator',
                ],
            ],
        ],
        'admin-password_forgot' => [
            'title' => 'Password dimenticata'
        ],
        'admin-password_reset' => [
            'title' => 'Reset della password'
        ],
        'admin-password_change' => [
            'title' => 'Cambio della password'
        ],
        'admin-verify' => [
            'title' => 'Verifica indirizzo email'
        ],
        'admin-verify_resend' => [
            'title' => 'Invio nuova mail di verifica'
        ],
        '403' => [
            'title' => 'Accesso negato',
            'description' => 'Non hai le autorizzazioni necessarie per accedere alla pagina.'
        ],
        '404' => [
            'title' => 'Pagina non trovata',
            'not_found' => 'La pagina :page non esiste.'
        ],
        '429' => [
            'title' => 'Troppe richieste',
            'description' => 'Spiacenti, sono pervenute troppe richieste.'
        ],
        '500' => [
            'title' => "Errore dell'applicazione",
            'description' => "Si è verificato un errore inaspettato.\nSe dovesse ripetersi ti preghiamo di contattarci.",
            'elasticsearch_description' => 'Unable to retrieve logs.',
        ],
    ],

];
