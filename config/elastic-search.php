<?php

return [
    'host' => env('ELASTICSEARCH_HOST', '127.0.0.1'),
    'port' => env('ELASTICSEARCH_PORT', 9200),
    'index_name' => env('ELASTICHSEARCH_INDEX_NAME', 'wai-log'),
    'ignore_exceptions' => env('ELASTICSEARC_IGNORE_EXCEPTIONS', false),
    'search_template_name' => env('ELASTICSEARCH_SEARCH_TEMPLATE_NAME', 'log_search'),
];
