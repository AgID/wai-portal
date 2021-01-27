<?php

namespace App\Http\Controllers;

use App\Models\Key;
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
            'keys' => 'api-key.index',
        ], [], $publicAdministration);

        $keys = Key::all();
        $hasKeys = count($keys) !== 0;

        $config = [
            'apiUrl' => $kongApiUrl,
            'production' => app()->environment('production'),
            'keysList' => $keys,
            'haskeys' => $hasKeys
        ];

        return view('pages.swagger')->with($roleAwareUrls)->with($config);
    }
}
