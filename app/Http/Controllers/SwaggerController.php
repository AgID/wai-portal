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
        $publicAdministration = ($publicAdministration->id ?? false) ? $publicAdministration : current_public_administration();

        $roleAwareUrls = $this->getRoleAwareUrlArray([
            'keys' => 'api-key.index',
        ], [], $publicAdministration);

        return view('pages.swagger')->with($roleAwareUrls);
    }
}
