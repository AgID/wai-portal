<?php

namespace App\Http\Controllers;

use App\Models\PublicAdministration;
use App\Models\Website;
use App\Transformers\WebsiteTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;
use Silber\Bouncer\BouncerFacade as Bouncer;

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
                'last_month_visits' => 'Visite*',
                'actions' => 'Azioni'
            ],
            'source' => route('websites-data-json'),
            'caption' => 'Elenco dei siti web abilitati su Web Analytics Italia', //TODO: set title in lang file
            'footer' => '*Il numero di visite si riferisce agli ultimi 30 giorni.',
            'columnsOrder' => [['added_at', 'asc'], ['last_month_visits', 'desc']]
        ];

        return view('pages.websites.index')->with($datatable);
    }

    public function createPrimary()
    {
        if (!empty(auth()->user()->getWebsites())) {
            return redirect(route('websites-index'));
        }
        return view('pages.websites.add_primary');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storePrimary(Request $request)
    {
        $validator = validator($request->all(), [
            'public_administration_name' => 'required',
            'url' => 'required|unique:websites',
            'pec' => 'email|nullable',
            'ipa_code' => 'required|unique:public_administrations',
            'accept_terms' => 'required'
        ]);

        $redis = Redis::connection('ipaindex');
        $pa = $redis->command('hgetall', [$request->input('ipa_code')]);

        $validator->after(function ($validator) use ($pa) {
            if (empty($pa)) {
                $validator->errors()->add('public_administration_name', 'La PA selezionata non esiste'); //TODO: put error message in lang file
            }
        });

        if ($validator->fails()) {
            return redirect(route('websites-add-primary'))
                ->withErrors($validator)
                ->withInput();
        }

        $pa = $redis->command('hgetall', [$request->input('ipa_code')]);

        $publicAdministration = PublicAdministration::make([
            'ipa_code' => $pa['ipa_code'],
            'name' => $pa['name'],
            'pec_address' => isset($pa['pec']) ? $pa['pec'] : null,
            'status' => 'pending'
        ]);

        $analyticsId = app()->make('analytics-service')->registerSite('Sito istituzionale', $pa['site'], $publicAdministration->name); //TODO: put string in lang file

        if (empty($analyticsId)) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        $publicAdministration->save();

        Website::create([
            'name' => 'Sito istituzionale', //TODO: put in lang file
            'url' => $pa['site'],
            'type' => 'primary',
            'public_administration_id' => $publicAdministration->id,
            'analytics_id' => $analyticsId,
            'slug' => str_slug($pa['site']),
            'status' => 'pending'
        ]);

        $publicAdministration->users()->save($request->user());

        $request->user()->roles()->detach();
        Bouncer::scope()->to($publicAdministration->id);
        $request->user()->assign('reader');

        logger()->info('User '.auth()->user()->getInfo().' added a new website ['.$pa['site'].'] as primary website of "'.$publicAdministration->name.'"');

        return redirect(route('websites-index'))->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia.']); //TODO: put message in lang file
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.websites.add');
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
            'url' => 'required|url|unique:websites',
            'type' => 'required|in:secondary,webapp,testing'
        ]);

        $publicAdministration = auth()->user()->publicAdministration;
        $analyticsId = app()->make('analytics-service')->registerSite($validatedData['name'].' ['.$validatedData['type'].']', $validatedData['url'], $publicAdministration->name); //TODO: put string in lang file

        if (empty($analyticsId)) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        Website::create([
            'name' => $validatedData['name'],
            'url' => $validatedData['url'],
            'type' => $validatedData['type'],
            'public_administration_id' => $publicAdministration->id,
            'analytics_id' => $analyticsId,
            'slug' => str_slug($validatedData['url']),
            'status' => 'pending'
        ]);

        logger()->info('User '.auth()->user()->getInfo().' added a new website "'. $validatedData['name'] .'" ['.$validatedData['url'].'] as '.$validatedData['type'].' website of "'.$publicAdministration->name.'"');

        return redirect(route('websites-index'))->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia.']); //TODO: put message in lang file
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
     * @param Website $website
     * @return \Illuminate\Http\Response
     */
    public function edit(Website $website)
    {
        if ($website->type == 'primary') {
            abort(403, 'Non è permesso effettuare modifiche al sito istituzionale.');
        }
        return view('pages.websites.edit')->with(['website' => $website]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Website $website
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Website $website)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'url' => [
                'required',
                Rule::unique('websites')->ignore($website->id),
                'url'
            ],
            'type' => 'required|in:secondary,webapp,testing'
        ]);

        $updated = app()->make('analytics-service')->updateSite($website->analytics_id, $validatedData['name'].' ['.$validatedData['type'].']', $validatedData['url'], $website->publicAdministration->name); //TODO: put string in lang file

        if (!$updated) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        $website->fill([
            'name' => $validatedData['name'],
            'url' => $validatedData['url'],
            'type' => $validatedData['type'],
            'slug' => str_slug($validatedData['url']),
        ]);
        $website->save();

        logger()->info('User '.auth()->user()->getInfo().' updated website "'. $validatedData['name'] .'" ['.$validatedData['url'].'] as '.$validatedData['type'].' website of "'.$website->publicAdministration->name.'"');

        return redirect(route('websites-index'))->withMessage(['success' => 'Il sito "'. $validatedData['name'] .'" è stato modificato.']); //TODO: put message in lang file
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
        return view('pages.websites.javascript_snippet')->with(['javascriptSnippet' => trim($javascriptSnippet)]);
    }
}
