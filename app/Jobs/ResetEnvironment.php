<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Events\Jobs\EnvironmentResetCompleted;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;

class ResetEnvironment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $commands = [
        [
            'command' => 'migrate:fresh',
        ],
        [
            'command' => 'app:init-permissions',
        ],
        [
            'command' => 'db:seed',
        ],
        [
            'command' => 'view:clear',
        ],
        [
            'command' => 'route:clear',
        ],
        [
            'command' => 'config:clear',
        ],
        // [
        //     'command' => 'config:cache',
        // ],
        [
            'command' => 'clear-compiled',
        ],
        [
            'command' => 'app:update-users',
        ],
        [
            'command' => 'app:update-websites',
        ],
    ];

    public function handle(): void
    {
        if (!app()->environment('production')) {
            logger()->info(
                'Resetting "' . config('app.env') . '" environment',
                [
                    'job' => JobType::RESET_ENVIRONMENT,
                ],
            );

            Redis::connection(env('CACHE_CONNECTION'))->client()->flushdb();
            Redis::connection(env('SESSION_CONNECTION'))->client()->flushdb();
            Redis::connection(env('QUEUE_REDIS_CONNECTION'))->client()->flushdb();

            $results = collect($this->commands)->mapToGroups(function ($command) {
                try {
                    Artisan::call($command['command'], $command['parameters'] ?? []);

                    return [
                        'completed' => [
                            'command' => $command['command'],
                        ],
                    ];
                } catch (Exception $exception) {
                    report($exception);
                    $message = $exception->getMessage();

                    return [
                        'failed' => [
                            'command' => $command['command'],
                            'message' => $message,
                        ],
                    ];
                }
            });

            event(new EnvironmentResetCompleted(
                empty($results->get('completed')) ? [] : $results->get('completed')->all(),
                empty($results->get('failed')) ? [] : $results->get('failed')->all(),
            ));
        } else {
            logger()->info(
                'Not resetting "' . config('app.env') . '" environment',
                [
                    'job' => JobType::RESET_ENVIRONMENT,
                ],
            );
        }
    }
}
