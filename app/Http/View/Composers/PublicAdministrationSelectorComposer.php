<?php

namespace App\Http\View\Composers;

use App\Enums\UserRole;
use App\Models\PublicAdministration;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\View\View;

class PublicAdministrationSelectorComposer
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
     * Create a new PublicAdministrationSelectorComposer.
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
     * Bind publicAdministrationSelectorArray data to the view.
     *
     * @param View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $lastRoute = $this->request->route();
        $lastRouteName = $lastRoute->getName();
        $authUser = $this->request->user();

        if ($lastRoute->isFallback) {
            return;
        }

        $selectTenantRoute = route('publicAdministrations.select');
        switch ($lastRouteName) {
            case 'admin.dashboard':
            case 'home':
                if ($authUser && $authUser->isA(UserRole::SUPER_ADMIN)) {
                    $targetRoute = 'admin.publicAdministration.analytics';
                } else {
                    $targetRoute = 'analytics';
                }
                $targetRouteHasPublicAdministrationParam = true;

                break;
            default:
                $targetRoute = $lastRouteName;
                $targetRouteHasPublicAdministrationParam = true;
        }

        if ($authUser && $authUser->isA(UserRole::SUPER_ADMIN)) {
            $publicAdministrationSelectorArray = PublicAdministration::all([
                'ipa_code',
                'name',
            ])->sortBy('name')->toArray();
            $publicAdministrationShowSelector = true;
            $selectTenantRoute = route('admin.publicAdministrations.select');
        } elseif ($authUser) {
            $publicAdministrationSelectorArray = $authUser->publicAdministrations()->get()
            ->map(function ($publicAdministration) {
                return collect($publicAdministration->toArray())
                    ->only(['id', 'ipa_code', 'name', 'url'])
                    ->all();
            })->sortBy('name')->values()->toArray();
            $publicAdministrationShowSelector = count($publicAdministrationSelectorArray) > 1;
        } else {
            $publicAdministrationSelectorArray = [];
            $publicAdministrationShowSelector = false;
        }

        $view->with('publicAdministrationSelectorArray', $publicAdministrationSelectorArray);
        $view->with('publicAdministrationShowSelector', $publicAdministrationShowSelector);
        $view->with('selectTenantRoute', $selectTenantRoute);
        $view->with('targetRoute', $targetRoute);
        $view->with('targetRouteHasPublicAdministrationParam', $targetRouteHasPublicAdministrationParam);
    }
}
