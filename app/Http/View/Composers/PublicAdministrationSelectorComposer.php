<?php

namespace App\Http\View\Composers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
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

        switch ($lastRouteName) {
            case 'admin.publicAdministrations.select':
                $returnRoute = 'admin.publicAdministration.analytics';
                $targetRouteHasPublicAdministrationParam = true;

                break;
            case 'publicAdministrations.select':
                $returnRoute = 'analytics';
                $targetRouteHasPublicAdministrationParam = true;

                break;
            case 'home':
                if ($authUser && $authUser->isA(UserRole::SUPER_ADMIN)) {
                    $returnRoute = 'admin.publicAdministration.analytics';
                } else {
                    $returnRoute = 'analytics';
                }
                $targetRouteHasPublicAdministrationParam = true;

                break;
            default:
                $returnRoute = $lastRouteName;
                $targetRouteHasPublicAdministrationParam = $this->request->has('publicAdministration');
        }

        if ($authUser && $authUser->isA(UserRole::SUPER_ADMIN)) {
            $publicAdministrationSelectorArray = PublicAdministration::all([
                'ipa_code',
                'name',
            ])->sortBy('name')->toArray();
            $publicAdministrationShowSelector = true;
        } elseif ($authUser) {
            $publicAdministrationSelectorArray = $authUser->publicAdministrations()->where('user_status', UserStatus::ACTIVE)
            ->orWhere('user_status', UserStatus::PENDING)
            ->get()->map(function ($publicAdministration) {
                return collect($publicAdministration->toArray())
                    ->only(['id', 'name', 'url'])
                    ->all();
            })->sortBy('name')->values()->toArray();
            $publicAdministrationShowSelector = count($publicAdministrationSelectorArray) > 1;
        } else {
            $publicAdministrationSelectorArray = [];
            $publicAdministrationShowSelector = false;
        }

        $view->with('publicAdministrationSelectorArray', $publicAdministrationSelectorArray);
        $view->with('publicAdministrationShowSelector', $publicAdministrationShowSelector);
        $view->with('returnRoute', $returnRoute);
        $view->with('targetRouteHasPublicAdministrationParam', $targetRouteHasPublicAdministrationParam);
    }
}
