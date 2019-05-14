<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Mail\PasswordReset;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPasswordResetEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The user this mail will be sent to.
     *
     * @var User
     */
    protected $user;

    /**
     * The token user for password reset.
     *
     * @var string
     */
    protected $token;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new PasswordReset($this->user, $this->token);
        Mail::to($this->user->email)->send($email);

        logger()->info(
            'Password reset mail sent to ' . $this->user->getInfo(),
            [
                'user' => $this->user->uuid,
                'job' => JobType::SEND_RESET_PASSWORD_TOKEN,
            ]
        );
    }
}
