<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Models\PasswordResetToken;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearPasswordResetToken implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $token;

    /**
     * Create a new job instance.
     *
     * @param PasswordResetToken $token
     */
    public function __construct(PasswordResetToken $token)
    {
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        logger()->info(
            'Deleting expired token ' . $this->token->token,
            [
                'user' => $this->token->user->uuid,
                'job' => JobType::CLEAR_PASSWORD_TOKEN,
            ]
        );

        $this->token->delete();
    }
}
