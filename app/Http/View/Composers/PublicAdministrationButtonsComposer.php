<?php

namespace App\Http\View\Composers;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\View\View;

class PublicAdministrationButtonsComposer
{
    /**
     * The current request.
     *
     * @var Request
     */
    protected $request;

    /**
     * The current session.
     *
     * @var Store
     */
    protected $session;

    /**
     * Create a new PrimaryMenuComposer.
     *
     * @param Request $request
     * @param Store $session
     */
    public function __construct(Request $request, Store $session)
    {
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Bind primaryMenuArray data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        switch ($this->request->route()->getName()) {
            case 'publicAdministrations.show':
                $showSelectButton = false;
                $showAddButton = true;
                break;
            case 'publicAdministrations.add':
                $showSelectButton = $this->request->user()->activePublicAdministrations->isNotEmpty();
                $showInvitedButton = true;
                break;
        }

        $view->with('showSelectButton', $showSelectButton ?? false);
        $view->with('showInvitedButton', $showInvitedButton ?? false);
        $view->with('showAddButton', $showAddButton ?? false);
    }
}
