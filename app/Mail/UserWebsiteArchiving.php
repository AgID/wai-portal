<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class UserWebsiteArchiving extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $user;

    protected $website;

    public function __construct(User $user, Website $website)
    {
        $this->user = $user;
        $this->website = $website;
    }

    public function build(): UserWebsiteArchiving
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject(trans('mail.website.archiving.user.subject'))
            ->markdown('mail.website_archiving_user_email')->with([
                'locale' => Lang::getLocale(),
                'fullName' => $this->user->full_name,
                'website' => $this->website->name,
            ]);
    }
}
