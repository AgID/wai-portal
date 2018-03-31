<?php

namespace App\Http\Controllers;

use App\Models\PublicAdministration;
use App\Models\Website;
use App\Transformers\WebsiteTransformer;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datatable = [
            'columns' => [
                'url' => 'URL',
                'type' => 'Tipo',
                'added_at' => 'Iscritto dal',
                'status' => 'Stato',
                'last_month_visits' => 'Visite ultimo mese',
                'actions' => 'Azioni'
            ],
            'source' => route('websites-data-json'),
            'caption' => 'Elenco dei siti web abilitati su Web Analytics Italia' //TODO: set title in lang file
        ];

        return view('pages.websites')->with($datatable);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.add_website');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'site' => 'required',
            'type' => 'required',
            'ipa_code' => 'required|exists:public_administrations',
            'accept_terms' => 'required'
        ]);

        $publicAdministration = PublicAdministration::findByIPACode($validatedData['ipa_code']);
        $analyticsId = app()->make('analytics-service')->registerSite($validatedData['site'].' ['.$validatedData['type'].']', $validatedData['site'], $publicAdministration->name); //TODO: put string in lang file

        if (empty($analyticsId)) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        Website::create([
            'public_administration_id' => $publicAdministration->id,
            'url' => $validatedData['site'],
            'type' => $validatedData['type'],
            'analytics_id' => $analyticsId,
            'slug' => str_slug($validatedData['site']),
            'status' => 'pending'
        ]);

        logger()->info('User '.auth()->user()->getInfo().' added a new website ['.$validatedData['site'].'] as '.$validatedData['type'].' website of "'.$publicAdministration->name.'"');

        return redirect(route('dashboard'))->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia']); //TODO: put message in lang file
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Get all websites of the specified Public Administration
     * in JSON format (to be consumed by Datatables)
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function dataJson()
    {
        return Datatables::of(auth()->user()->getWebsites())
                ->setTransformer(new WebsiteTransformer)
                ->make(true);
    }

    /**
     * Get Javascript snippet for the specified Website
     * of the specified Public Administration
     *
     * @param  Website $website
     * @return $this
     */
    public function showJavascriptSnippet(Website $website)
    {
        $javascriptSnippet = app()->make('analytics-service')->getJavascriptSnippet($website->analytics_id);
        return view('pages.show_javascript_snippet')->with(['javascriptSnippet' => trim($javascriptSnippet)]);
    }
}
