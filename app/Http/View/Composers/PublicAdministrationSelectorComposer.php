<?php

namespace App\Http\View\Composers;

use App\Enums\UserRole;
use App\Models\PublicAdministration;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
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
        $authUser = $this->request->user();

        $lastRouteParameters = $lastRoute->parameters();

        if ($lastRoute->isFallback) {
            return;
        }

        switch (true) {
            case Arr::has($lastRouteParameters, ['publicAdministration', 'user']):
                $newRoute = 'admin.publicAdministration.users.index';
                break;
            case Arr::has($lastRouteParameters, ['publicAdministration', 'website']):
                $newRoute = 'admin.publicAdministration.websites.index';
                break;
            case !Arr::has($lastRouteParameters, 'publicAdministration'):
                if ($this->request->has('publicAdministration')) {
                    if ($authUser->isA(UserRole::SUPER_ADMIN)) {
                        $this->session->put('super_admin_tenant_ipa_code', $this->request->input('publicAdministration'));
                    }
                }
                // no break
            default:
                $newRoute = $lastRoute->getName();
        }

        if ($authUser && $authUser->isA(UserRole::SUPER_ADMIN)) {
            $publicAdministrationSelectorArray = PublicAdministration::all([
                'ipa_code',
                'name',
            ])->sortBy('name')->map(function ($publicAdministration) use ($newRoute, $lastRouteParameters) {
                $publicAdministration->url = route($newRoute, array_merge($lastRouteParameters, [
                    'publicAdministration' => $publicAdministration->ipa_code,
                ]));

                return $publicAdministration;
            })->toArray();
            $publicAdministrationShowSelector = true;
        } elseif ($authUser) {
            $publicAdministrationSelectorArray = $authUser->publicAdministrations->sortBy('name')->values()
                ->map(function ($publicAdministration) use ($newRoute, $lastRouteParameters) {
                    $publicAdministration->url = route($newRoute, array_merge($lastRouteParameters, [
                        'publicAdministration' => $publicAdministration->id,
                    ]));

                    return collect($publicAdministration->toArray())
                        ->only(['id', 'name', 'url'])
                        ->all();
                })->toArray();
            $publicAdministrationShowSelector = count($publicAdministrationSelectorArray) > 1;
        } else {
            $publicAdministrationSelectorArray = [];
            $publicAdministrationShowSelector = false;
        }
        $view->with('publicAdministrationSelectorArray', $publicAdministrationSelectorArray);
        $view->with('publicAdministrationShowSelector', $publicAdministrationShowSelector);
    }
}
