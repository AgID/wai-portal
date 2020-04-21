<?php

namespace App\Http\View\Composers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\PublicAdministration;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
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

        if ($lastRoute->isFallback) {
            return;
        }

        switch ($this->request->route()->getName()) {
            case 'admin.publicAdministrations.select':
                $returnRoute = 'admin.publicAdministration.analytics';
                $hasRouteParampublicAdministration = true;
                break;
            case 'publicAdministrations.select':
                $returnRoute = 'analytics';
                $hasRouteParampublicAdministration = true;
                break;
            case 'home':
                if ($authUser && $authUser->isA(UserRole::SUPER_ADMIN)) {
                    $returnRoute = 'admin.publicAdministration.analytics';
                } elseif ($authUser) {
                    $returnRoute = 'analytics';
                } else {
                    $returnRoute = $this->request->route()->getName();
                }
                $hasRouteParampublicAdministration = true;
                break;
            default:
                $returnRoute = $this->request->route()->getName();
                $hasRouteParampublicAdministration = $this->request->route('publicAdministration') ? true : false;
                break;
        }

        if ($authUser && $authUser->isA(UserRole::SUPER_ADMIN)) {
            $publicAdministrationSelectorArray = PublicAdministration::all([
                'ipa_code',
                'name',
            ])->sortBy('name')->toArray();
            $publicAdministrationShowSelector = true;
            $postRoute = route('admin.publicAdministrations.change');
        } elseif ($authUser) {
            $publicAdministrationSelectorArray = $authUser->publicAdministrations()->where('pa_status', UserStatus::ACTIVE)
            ->orWhere('pa_status', UserStatus::PENDING)
            ->get()->map(function ($publicAdministration) {
                return collect($publicAdministration->toArray())
                    ->only(['id', 'name', 'url'])
                    ->all();
            })->sortBy('name')->values()->toArray();
            $publicAdministrationShowSelector = count($publicAdministrationSelectorArray) > 1;
            $postRoute = route('publicAdministrations.change');
        } else {
            $publicAdministrationSelectorArray = [];
            $publicAdministrationShowSelector = false;
            $postRoute = route('publicAdministrations.change');
        }

        $view->with('publicAdministrationSelectorArray', $publicAdministrationSelectorArray);
        $view->with('publicAdministrationShowSelector', $publicAdministrationShowSelector);
        $view->with('postRoute', $postRoute);
        $view->with('returnRoute', $returnRoute);
        $view->with('hasRouteParampublicAdministration', $hasRouteParampublicAdministration);
    }
}
