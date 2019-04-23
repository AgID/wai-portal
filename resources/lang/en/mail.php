<?php

return [

    'website' => [
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
