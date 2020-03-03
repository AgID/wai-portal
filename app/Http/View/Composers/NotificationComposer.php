<?php

namespace App\Http\View\Composers;

use Illuminate\Session\Store;
use Illuminate\View\View;

class NotificationComposer
{
    /**
     * The current session.
     *
     * @var Store
     */
    protected $session;

    /**
     * Create a new NotificationComposer.
     *
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Bind notification data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        if (session()->has('notification')) {
            $view->with('notification', session()->get('notification'));
        }
    }
}
