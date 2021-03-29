<?php

use App\Services\ElasticSearchService;
use Monolog\Formatter\ElasticsearchFormatter;
use Monolog\Handler\ElasticsearchHandler;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', env('LOG_STACK_CHANNELS', 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/application.log'),
            'level' => 'debug',
        ],

        'testing' => [
            'driver' => 'single',
            'path' => storage_path('logs/testing.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/application.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'elasticsearch' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => ElasticsearchHandler::class,
            'handler_with' => [
                'client' => app(ElasticSearchService::class)->getClient(),
                'options' => [
                    'index' => config('elastic-search.index_name'),
                    'ignore_error' => config('elastic-search.ignore_exceptions'),
                ],
            ],
            'formatter' => ElasticsearchFormatter::class,
            'formatter_with' => [
                'index' => config('elastic-search.index_name'),
            ],
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL', ''),
            'username' => env('LOG_SLACK_USERNAME', config('app.name')),
            'tap' => [App\Logging\SlackLogger::class],
            'level' => 'notice',
            'exclude_fields' => [
                'context.exception',
            ],
        ],

        'slack_extra_channel' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL_EXTRA_CHANNEL', ''),
            'username' => env('LOG_SLACK_USERNAME_EXTRA_CHANNEL', config('app.name')),
            'tap' => [App\Logging\SlackLogger::class],
            'level' => 'notice',
            'exclude_fields' => [
                'context.exception',
            ],
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stdout' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stdout',
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],
    ],

];
