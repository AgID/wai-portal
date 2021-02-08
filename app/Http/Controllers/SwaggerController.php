<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use App\Models\PublicAdministration;
use App\Traits\HasRoleAwareUrls;
use Illuminate\Http\Request;

class SwaggerController extends Controller
{
    use HasRoleAwareUrls;

    public function index(Request $request, PublicAdministration $publicAdministration)
    {
        $kongApiUrl = config('kong-service.api_url');
        $publicAdministration = ($publicAdministration->id ?? false) ? $publicAdministration : current_public_administration();

        if (null === $publicAdministration) {
            return redirect()->route('websites.index');
        }

        $roleAwareUrls = $this->getRoleAwareUrlArray([
            'credentials' => 'api-credentials.index',
        ], [], $publicAdministration);

        $credentials = Credential::all();
        $hasCredentials = 0 !== count($credentials);

        $config = [
            'apiUrl' => $kongApiUrl,
            'production' => app()->environment('production'),
            'credentialsList' => $credentials,
            'hascredentials' => $hasCredentials,
        ];

        return view('pages.swagger')->with($roleAwareUrls)->with($config);
    }
}
