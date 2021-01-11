<?php

return [
    'api_key' => env('SDG_API_KEY'),
    'api_public_url' => env('SDG_API_PUBLIC_URL'),
    'ssl_verify' => env('SDG_API_SSL_VERIFY'),
    'last_days' => env('SDG_API_LAST_DAYS'),
    'start_date' => env('SDG_API_START_DATE'),
    'end_date' => env('SDG_API_END_DATE'),
    'storage_folder' => env('SDG_STORAGE_FOLDER', 'sdg'),
    'storage_disk' => env('SDG_STORAGE_DISK', 'persistent'),
    'url_column_index_csv' => env('SDG_COLUMN_INDEX_URL_CSV'),
    'url_column_separator_csv' => env('SDG_COLUMN_SEPARATOR_CSV', ','),
];