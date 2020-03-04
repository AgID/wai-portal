<?php

namespace App\Http\Controllers;

use App\Enums\PublicAdministrationStatus;
use App\Events\PublicAdministration\PublicAdministrationRegistered;
use App\Events\Website\WebsiteAdded;
use App\Http\Requests\StoreCustomPrimaryWebsiteRequest;
use App\Models\PublicAdministration;
use App\Traits\ManagePublicAdministrationRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomPublicAdministrationController extends Controller
{
    use ManagePublicAdministrationRegistration;

    public function index(): View
    {
        return view('pages.websites.partials.add_custom_primary');
    }

    public function store(StoreCustomPrimaryWebsiteRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $authUser = $request->user();

        $publicAdministration = PublicAdministration::make([
            'ipa_code' => Str::uuid(),
            'name' => $validatedData['name'],
            'pec' => $validatedData['pec'] ?? null,
            'rtd_name' => $validatedData['rtd_name'] ?? null,
            'rtd_mail' => $validatedData['rtd_mail'] ?? null,
            'rtd_pec' => $validatedData['rtd_pec'] ?? null,
            'city' => $validatedData['city'],
            'county' => $validatedData['county'],
            'region' => $validatedData['region'],
            'type' => $validatedData['type'],
            'status' => PublicAdministrationStatus::PENDING,
        ]);

        $website = $this->registerPublicAdministration($authUser, $publicAdministration, $validatedData['site']);

        event(new PublicAdministrationRegistered($publicAdministration, $authUser));
        event(new WebsiteAdded($website, $authUser));

        return redirect()->route('websites.index')->withModal([
            'title' => __('Il sito è stato inserito, adesso procedi ad attivarlo!'),
            'icon' => 'it-check-circle',
            'message' => __('Abbiamo inviato al tuo indirizzo email le istruzioni per attivare il sito e iniziare a monitorare il traffico.'),
            'image' => asset('images/primary-website-added.svg'),
        ]);
    }
}
