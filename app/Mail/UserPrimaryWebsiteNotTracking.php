<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class UserPrimaryWebsiteNotTracking extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build(): UserPrimaryWebsiteNotTracking
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(__('mail.website.primary_not_tracking.user.subject'))
            ->markdown('mail.primary_website_not_tracking_user_email')->with([
                'locale' => Lang::getLocale(),
                'fullName' => $this->user->full_name,
            ]);
    }
}
