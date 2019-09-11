<?php

namespace App\Http\View\Composers;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\View\View;

class PrimaryMenuComposer
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
        $isSuperAdmin = optional($this->request->user())->isA(UserRole::SUPER_ADMIN);
        $selectedPublicAdministrationIpaCode = $this->session->get('super_admin_tenant_ipa_code');
        $primaryMenuArray = collect(config('site.menu_items.primary'))->map(function ($primaryMenuItem) use ($isSuperAdmin, $selectedPublicAdministrationIpaCode) {
            if ($isSuperAdmin && !$selectedPublicAdministrationIpaCode) {
                $primaryMenuItem['url'] = '#';
                $primaryMenuItem['disabled'] = true;
            } else {
                $primaryMenuItem['url'] = $isSuperAdmin
                ? route('admin.publicAdministration.' . $primaryMenuItem['route'], [
                    'publicAdministration' => $selectedPublicAdministrationIpaCode,
                ], false)
                : route($primaryMenuItem['route'], [], false);
                $primaryMenuItem['disabled'] = false;
            }

            $primaryMenuItem['active'] = $this->request->is(trim($primaryMenuItem['url'], '/') . '*');

            return $primaryMenuItem;
        })->toArray();

        $view->with('primaryMenuArray', $primaryMenuArray);
    }
}
