<?php

return [

    'website' => [
        'primary_not_tracking' => [
            'user' => [
                'title' => 'Tracciamento non attivo su sito istituzionale',
                'subject' => '[Warning] - Mancato tracciamento sito istituzionale',
            ],
        ],
        'activated' => [
            'user' => [
                'title' => 'Tracciamento sito web attivo',
                'subject' => '[Info] - Sito web attivo',
            ],
        ],
        'purging' => [
            'user' => [
                'title' => 'Sito web in cancellazione',
                'subject' => '[Warning] - Avviso rimozione',
            ],
        ],
        'archiving' => [
            'user' => [
                'title' => 'Nessna attività rilevata sul sito web',
                'subject' => '[Warning] - Avviso archiviazione',
            ],
        ],
    ],

    'user' => [
        'expired_invitation_link_visited' => [
            'user' => [
                'title' => 'Invito scaduto rilevato',
            ],
        ],
    ],

];
