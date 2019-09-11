<?php

namespace App\Http\View\Composers;

use Illuminate\Session\Store;
use Illuminate\View\View;

class ModalComposer
{
    /**
     * The current session.
     *
     * @var Store
     */
    protected $session;

    /**
     * Create a new ModalComposer.
     *
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Bind modal data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        if (session()->has('modal')) {
            $view->with('modal', session()->get('modal'));
        }
    }
}
