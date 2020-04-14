<?php

namespace App\Http\Controllers;

class PublicAdministrationController extends Controller
{
    /**
     * Show the Public Administration selector.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function selectTenant()
    {
        return view('pages.select_tenant')->with('hasTenant', session()->has('tenant_id'));
    }
}
