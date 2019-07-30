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
    'scrolltop_label' => "torna all'inizio dei contenuti",
    'cookiebar_msg' => 'Questo sito utilizza cookie tecnici, analytics e di terze parti. Proseguendo nella navigazione accetti l’utilizzo dei cookie.',
    'cookiebar_accept' => 'Accetto',
    'cookiebar_privacy_policy' => 'Privacy policy',
    'session_expired' => 'La sessione è scaduta per inattività.',
    'return_home' => 'Ritorna alla pagina iniziale.',

    'pages' => [
        'home' => [
            'title' => 'Home',
        ],
        'faq' => [
            'title' => 'Domande frequenti',
        ],
        'privacy' => [
            'title' => 'Privacy',
        ],
        'legal-notes' => [
            'title' => 'Note legali',
        ],
        'dashboard' => [
            'title' => 'Dashboard',
            'websites' => 'Siti web',
            'users' => 'Utenti',
        ],
        'websites' => [
            'index' => [
                'title' => 'Siti web tracciati',
                'view_javascript_snippet' => 'vedi snippet JS',
                'go_to_analytics_service' => 'vai agli analytics',
                'add_website' => 'aggiungi sito',
                'edit_website' => 'modifica sito',
                'check_tracking' => 'verifica attivazione',
                'archive' => 'archivia',
                'enable' => 'riabilita',
            ],
            'add-primary' => [
                'title' => 'Sito istituzionale',
            ],
            'add' => [
                'title' => 'Nuovo sito',
            ],
            'edit' => [
                'title' => 'Modifica sito',
            ],
            'javascript-snippet' => [
                'title' => 'Snippet di codice Javascript',
            ]
        ],
        'users' => [
            'index' => [
                'title' => 'Utenti',
                'show_user' => 'dettaglio utente',
                'add_user' => 'aggiungi utente',
                'edit_user' => 'modifica utente',
                'suspend_user' => 'sospendi utente',
                'reactivate_user' => 'riattiva utente',
                'restore_user' => 'ripristina utente',
                'delete_user' => 'rimuovi utente',
            ],
            'show' => [
                'title' => 'Visualizza utente',
            ],
            'add' => [
                'title' => 'Nuovo utente',
            ],
            'edit' => [
                'title' => 'Modifica utente',
            ],
        ],
        'spid-auth_login' => [
            'title' => 'Accesso con SPID',
        ],
        'profile' => [
            'show' => [
                'title' => 'Profilo utente',
            ],
            'edit' => [
                'title' => 'Profilo utente',
            ],
        ],
        'auth' => [
            'register' => [
                'title' => 'Registrazione',
            ],
        ],
        'auth-verify' => [
            'title' => 'Verifica indirizzo email',
        ],
        'logs' => [
            'title' => 'Visualizzazione log',
            'form' => [
                'legend' => 'Filtri di ricerca',
                'inputs' => [
                    'start_date' => [
                        'label' => 'Data di inizio (gg/mm/aaaa)',
                    ],
                    'start_time' => [
                        'label' => 'Orario di inizio (HH:mm)',
                    ],
                    'end_date' => [
                        'label' => 'Data di fine (gg/mm/aaaa)',
                    ],
                    'end_time' => [
                        'label' => 'Orario di fine (HH:mm)',
                    ],
                    'message' => [
                        'label' => 'Messaggio',
                    ],
                    'severity' => [
                        'label' => 'Gravità minima',
                    ],
                    'event' => [
                        'label' => 'Evento',
                        'empty-selection' => 'Tutti',
                    ],
                    'exception' => [
                        'label' => 'Errore',
                        'empty-selection' => 'Tutte',
                    ],
                    'job' => [
                        'label' => 'Attività',
                        'empty-selection' => 'Tutte',
                    ],
                    'pa' => [
                        'label' => 'Pubbliche Amministrazioni',
                    ],
                    'website' => [
                        'label' => 'Siti web',
                    ],
                    'user' => [
                        'label' => 'Utenti',
                    ],
                ],
                'submit' => 'Visualizza',
            ],
            'table' => [
                'headers' => [
                    'time' => 'Orario',
                    'message' => 'Messaggio',
                    'level' => 'Gravità',
                    'trace' => 'Pila Chiamate',
                ],
                'caption' => 'Messaggi di log del portale Web Analytics Italia',
            ],
        ],
        'admin' => [
            'dashboard' => [
                'title' => 'Dashboard amministrativa',
            ],
            'users' => [
                'index' => [
                    'title' => 'Utenti amministratori',
                    'show_user' => 'dettaglio amministratore',
                    'add_user' => 'aggiungi amministratore',
                    'edit_user' => 'modifica amministratore',
                    'suspend_user' => 'sospendi amministratore',
                    'reactivate_user' => 'riattiva amministratore',
                ],
                'show' => [
                    'title' => 'Visualizza amministratore',
                ],
                'add' => [
                    'title' => 'Aggiungi amministratore',
                ],
                'edit' => [
                    'title' => 'Modifica amministratore',
                ],
            ],
        ],
        'admin-login' => [
            'title' => 'Accesso amministratori',
        ],
        'admin-password_forgot' => [
            'title' => 'Password dimenticata',
        ],
        'admin-password_reset' => [
            'title' => 'Reset della password',
        ],
        'admin-password_change' => [
            'title' => 'Cambio della password',
        ],
        'admin-verify' => [
            'title' => 'Verifica indirizzo email',
        ],
        'admin-verify_resend' => [
            'title' => 'Invio nuova mail di verifica',
        ],
        '403' => [
            'title' => 'Accesso negato',
            'description' => 'Non hai le autorizzazioni necessarie per accedere alla pagina.',
        ],
        '404' => [
            'title' => 'Pagina non trovata',
            'not_found' => 'La pagina :page non esiste.',
        ],
        '429' => [
            'title' => 'Troppe richieste',
            'description' => 'Spiacenti, sono pervenute troppe richieste.',
        ],
        '500' => [
            'title' => "Errore dell'applicazione",
            'description' => "Si è verificato un errore inaspettato.\nSe dovesse ripetersi ti preghiamo di contattarci.",
            'elasticsearch_description' => 'Si è verificato un errore nel recupero dei log.',
        ]
    ],

];
