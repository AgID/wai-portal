<?php

namespace App\Jobs;

use App\Models\PasswordResetToken;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        logger()->info("Deleting expired token " . $this->token->token);
        $this->token->delete();
    }
}
