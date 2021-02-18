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
            'production' => app()->environment('production'),
            'credentialsList' => $credentials,
            'hascredentials' => $hasCredentials,
        ];

        return view('pages.swagger')->with($roleAwareUrls)->with($config);
    }

    public function apiSpecification()
    {
        $path = resource_path('data/api.json');

        if (!is_file($path) || !is_readable($path)) {
            return response()
                ->json(['error' => 'API configuration file not readable'], 500);
        }

        $data = json_decode(file_get_contents($path));
        $apiUrl = config('kong-service.api_url');

        $data->servers = [
            [
                'url' => $apiUrl,
                'description' => 'API Gateway',
            ],
        ];

        $data->components
            ->securitySchemes
            ->oAuth
            ->flows
            ->clientCredentials
            ->tokenUrl = $apiUrl . '/portal/oauth2/token';

        return response()
            ->json($data, 200);
    }
}
