<?php

namespace App\Jobs;

use App\Models\PasswordResetToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearPasswordResetToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * @throws \Exception
     *
     * @return void
     */
    public function handle()
    {
        logger()->info('Deleting expired token ' . $this->token->token);
        $this->token->delete();
    }
}
