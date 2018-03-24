<?php

namespace App\Http\Controllers;

use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Silber\Bouncer\BouncerFacade as Bouncer;

class PublicAdministrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = validator($request->all(), [
            'name' => 'required',
            'site' => 'required',
            'pec' => 'email|nullable',
            'ipa_code' => 'required|unique:public_administrations',
            'accept_terms' => 'required'
        ]);

        $redis = Redis::connection('ipaindex');

        $validator->after(function ($validator) use ($request, $redis) {
            $pa = $redis->command('hgetall', [$request->input('ipa_code')]);
            if (empty($pa)) {
                $validator->errors()->add('name', 'La PA selezionata non esiste'); //TODO: put error message in lang file
            }
        });

        if ($validator->fails()) {
            return redirect(route('add-primary-website')) //TODO: redirect to proper view
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

        $analyticsId = app()->make('analytics-service')->registerSite($pa['site'].' [sito istituzionale]', $pa['site'], $publicAdministration->name); //TODO: put string in lang file

        if (empty($analyticsId)) {
            abort(500, 'Il servizio Analytics non è disponibile'); //TODO: put error message in lang file
        }

        $publicAdministration->save();

        Website::create([
            'public_administration_id' => $publicAdministration->id,
            'url' => $pa['site'],
            'type' => 'primary',
            'analytics_id' => $analyticsId,
            'slug' => str_slug($pa['site']),
            'status' => 'pending'
        ]);

        $publicAdministration->users()->save($request->user());

        Bouncer::scope()->to($publicAdministration->id);
        $request->user()->assign('reader');

        logger()->info('User '.auth()->user()->getInfo().' added a new website ['.$pa['site'].'] as primary website of "'.$publicAdministration->name.'"');

        return redirect(route('dashboard'))->withMessage(['success' => 'Il sito è stato aggiunto al progetto Web Analytics Italia']); //TODO: put message in lang file

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
