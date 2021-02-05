<?php

namespace App\Http\Controllers;

use App\Models\PublicAdministration;
use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return View
     */
    public function dashboard(): View
    {
        $paDatatable = [
            'datatableOptions' => [
                'searching' => [
                    'label' => __('cerca tra le amministrazioni'),
                ],
                'columnFilters' => [
                    'name' => [
                        'filterLabel' => __('nome'),
                    ],
                    'status' => [
                        'filterLabel' => __('stato'),
                    ],
                ],
            ],
            'columns' => [
                ['data' => 'name', 'name' => __('nome'), 'className' => 'text-wrap'],
                ['data' => 'status', 'name' => __('stato')],
                ['data' => 'websites_total', 'name' => __('siti totali'), 'className' => 'text-right'],
                ['data' => 'websites_active', 'name' => __('siti attivi'), 'className' => 'text-right'],
                ['data' => 'added_at', 'name' => __('aggiunta il'), 'className' => 'text-right'],
                ['data' => 'buttons', 'name' => '', 'orderable' => false],
            ],
            'source' => route('admin.publicAdministrations.data.json'),
            'caption' => __('elenco delle pubbliche amministrazioni su :app', ['app' => config('app.name')]),
            'columnsOrder' => [['added_at', 'desc']],
        ];

        return view('pages.admin.dashboard')->with($paDatatable)->with('hasPublicAdministrations', PublicAdministration::all()->isNotEmpty());
    }
}
