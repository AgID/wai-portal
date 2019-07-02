<?php

return [

    'website' => [
        'primary_not_tracking' => [
            'user' => [
                'title' => 'No tracking from primary website',
                'subject' => '[Warning] - Primary website not tracking',
            ],
        ],
        'activated' => [
            'user' => [
                'title' => 'Website tracking activated',
                'subject' => '[Info] - Website active',
            ],
        ],
        'purging' => [
            'user' => [
                'title' => 'Website scheduled for deleting',
                'subject' => '[Warning] - Website removal scheduled',
            ],
        ],
        'archiving' => [
            'user' => [
                'title' => 'No activity tracked on website',
                'subject' => '[Warning] - Website archiving scheduled',
            ],
        ],
    ],

];
