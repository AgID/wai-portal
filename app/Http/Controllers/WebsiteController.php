<?php

namespace App\Http\Controllers;

use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Models\PublicAdministration;
use App\Models\Website;
use App\Transformers\WebsiteTransformer;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PhpRedisAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Silber\Bouncer\BouncerFacade as Bouncer;
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
                'last_month_visits' => 'Visite*',
                'actions' => 'Azioni',
            ],
            'source' => route('websites-data-json'),
            'caption' => 'Elenco dei siti web abilitati su Web Analytics Italia', //TODO: set title in lang file
            'footer' => '*Il numero di visite si riferisce agli ultimi 30 giorni.',
            'columnsOrder' => [['added_at', 'asc'], ['last_month_visits', 'desc']],
        ];

        return view('pages.websites.index')->with($datatable);
    }

    public function createPrimary()
    {
        if (auth()->user()->publicAdministrations->isNotEmpty()) {
            return redirect()->route('websites-index');
        }

        return view('pages.websites.add_primary');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function storePrimary(Request $request)
    {
        $validator = validator($request->all(), [
            'public_administration_name' => 'required',
            'url' => 'required|unique:websites',
            'pec' => 'email|nullable',
            'ipa_code' => 'required|unique:public_administrations',
            'accept_terms' => 'required',
        ]);

        $IPAIndex = new Index((new PhpRedisAdapter())->connect(config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');

        $result = $IPAIndex->inFields(1, ['ipa_code'])
            ->search($request->input('ipa_code'))
            ->getDocuments();

        $pa = empty($result) ? null : $result[0];

        $validator->after(function ($validator) use ($pa) {
            if (empty($pa)) {
                $validator->errors()->add('public_administration_name', 'La PA selezionata non esiste'); //TODO: put error message in lang file
            }
        });

        if ($validator->fails()) {
            return redirect()->route('websites-add-primary')
                ->withErrors($validator)
                ->withInput();
        }

        $publicAdministration = PublicAdministration::make([
            'ipa_code' => $pa->ipa_code,
            'name' => $pa->name,
            'pec_address' => $pa->pec ?? null,
            'city' => $pa->city,
            'county' => $pa->county,
            'region' => $pa->region,
            'type' => $pa->type,
            'status' => WebsiteStatus::PENDING,
        ]);

        $analyticsId = app()->make('analytics-service')->registerSite('Sito istituzionale', $pa->site, $publicAdministration->name); //TODO: put string in lang file

        if (empty($analyticsId)) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        $publicAdministration->save();

        Website::create([
            'name' => 'Sito istituzionale', //TODO: put in lang file
            'url' => $pa->site,
            'type' => WebsiteType::PRIMARY,
            'public_administration_id' => $publicAdministration->id,
            'analytics_id' => $analyticsId,
            'slug' => Str::slug($pa->site),
            'status' => WebsiteStatus::PENDING,
        ]);

        $publicAdministration->users()->save($request->user());

        $request->user()->roles()->detach();
        session()->put('tenant_id', $publicAdministration->id);
        Bouncer::scope()->to($publicAdministration->id);
        $request->user()->assign('reader');

        logger()->info('User ' . auth()->user()->getInfo() . ' added a new website [' . $pa->site . '] as primary website of "' . $publicAdministration->name . '"');

        return redirect()->route('websites-index')->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia.']); //TODO: put message in lang file
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => 'required',
            'url' => 'required|url|unique:websites',
            'type' => 'required|in:primary,secondary,webapp,testing',
        ]);

        $IPAIndex = new Index((new PhpRedisAdapter())->connect(config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');

        $domain = parse_url($request->input('url'), PHP_URL_HOST);
        $result = $IPAIndex->inFields(1, ['site'])
            ->search(str_replace([':', '-', '@'], ['\:', '\-', '\@'], $domain))
            ->getDocuments();

        $sameUrlInOtherPA = !empty($result) && $domain == $result[0]->site;

        $validator->after(function ($validator) use ($sameUrlInOtherPA) {
            if ($sameUrlInOtherPA) {
                $validator->errors()->add('url', "L'indirizzo inserito appartiene ad un'altra PA."); //TODO: put error message in lang file
            }
        });

        if ($validator->fails()) {
            return redirect()->route('websites-add')
                ->withErrors($validator)
                ->withInput();
        }

        $analyticsId = app()->make('analytics-service')->registerSite($request->input('name') . ' [' . $request->input('type') . ']', $request->input('url'), $publicAdministration->name); //TODO: put string in lang file

        if (empty($analyticsId)) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        Website::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'type' => $request->input('type'),
            'public_administration_id' => session('tenant_id'),
            'analytics_id' => $analyticsId,
            'slug' => Str::slug($request->input('url')),
            'status' => WebsiteStatus::PENDING,
        ]);

        logger()->info('User ' . auth()->user()->getInfo() . ' added a new website "' . $validatedData['name'] . '" [' . $validatedData['url'] . '] as ' . $validatedData['type'] . ' website of "' . $publicAdministration->name . '"');

        return redirect()->route('websites-index')->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia.']); //TODO: put message in lang file
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
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
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Website $website)
    {
        if ('primary' == $website->type) {
            abort(403, 'Non è permesso effettuare modifiche al sito istituzionale.');
        }

        return view('pages.websites.edit')->with(['website' => $website]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Website $website
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Website $website)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'url' => [
                'required',
                Rule::unique('websites')->ignore($website->id),
                'url',
            ],
            'type' => 'required|in:secondary,webapp,testing',
        ]);

        $updated = app()->make('analytics-service')->updateSite($website->analytics_id, $validatedData['name'] . ' [' . $validatedData['type'] . ']', $validatedData['url'], $website->publicAdministration->name); //TODO: put string in lang file

        if (!$updated) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        $website->fill([
            'name' => $validatedData['name'],
            'url' => $validatedData['url'],
            'type' => $validatedData['type'],
            'slug' => Str::slug($validatedData['url']),
        ]);
        $website->save();

        logger()->info('User ' . auth()->user()->getInfo() . ' updated website "' . $validatedData['name'] . '" [' . $validatedData['url'] . '] as ' . $validatedData['type'] . ' website of "' . $website->publicAdministration->name . '"');

        return redirect()->route('websites-index')->withMessage(['success' => 'Il sito "' . $validatedData['name'] . '" è stato modificato.']); //TODO: put message in lang file
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Get all websites of the specified Public Administration
     * in JSON format (to be consumed by Datatables).
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function dataJson()
    {
        return Datatables::of(current_public_administration()->websites())
                ->setTransformer(new WebsiteTransformer())
                ->make(true);
    }

    /**
     * Get Javascript snippet for the specified Website
     * of the specified Public Administration.
     *
     * @param Website $website
     *
     * @return $this
     */
    public function showJavascriptSnippet(Website $website)
    {
        $javascriptSnippet = app()->make('analytics-service')->getJavascriptSnippet($website->analytics_id);

        return view('pages.websites.javascript_snippet')->with(['javascriptSnippet' => trim($javascriptSnippet)]);
    }
}
