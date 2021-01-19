<?php

namespace App\Http\Controllers;

use App\Models\PublicAdministration;
use App\Traits\HasRoleAwareUrls;
use Illuminate\Http\Request;

class SwaggerController extends Controller
{
    use HasRoleAwareUrls;

    public function index(Request $request, PublicAdministration $publicAdministration)
    {
        $kongApiUrl = config('kong-service.endpoint_url');
        $publicAdministration = ($publicAdministration->id ?? false) ? $publicAdministration : current_public_administration();

        if(null === $publicAdministration){
            return redirect()->route('websites.index');
        }

        $roleAwareUrls = $this->getRoleAwareUrlArray([
            'keys' => 'api-key.index'
        ], [], $publicAdministration);

        $config = [
            'apiUrl' => $kongApiUrl
        ];

        return view('pages.swagger')->with($roleAwareUrls)->with($config);
    }
}
