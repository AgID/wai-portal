<?php

namespace App\Http\View\Composers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
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
            case 'admin.publicAdministrations.select':
            case 'publicAdministrations.select':
                $showAddButton = true;
                $showInvitedButton = true;
                break;
            case 'admin.publicAdministrations.show':
            case 'publicAdministrations.show':
                $showSelectButton = $this->request->user()->publicAdministrations()->where('pa_status', UserStatus::ACTIVE)->get()->isNotEmpty();
                $showAddButton = true;
                break;
            case 'admin.publicAdministrations.add':
            case 'publicAdministrations.add':
                $showSelectButton = $this->request->user()->publicAdministrations()->where('pa_status', UserStatus::ACTIVE)->get()->isNotEmpty();
                $showInvitedButton = true;
                break;
        }

        $view->with('isSuperAdmin', optional($this->request->user())->isA(UserRole::SUPER_ADMIN));
        $view->with('showSelectButton', $showSelectButton ?? false);
        $view->with('showInvitedButton', $showInvitedButton ?? false);
        $view->with('showAddButton', $showAddButton ?? false);
        $view->with('hasTenant', $this->session->has('tenant_id') ?? false);
    }
}
