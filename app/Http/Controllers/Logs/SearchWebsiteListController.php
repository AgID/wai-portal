<?php

namespace App\Http\Controllers\Logs;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Traits\InteractsWithWebsiteIndex;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Websites index controller.
 */
class SearchWebsiteListController extends Controller
{
    use InteractsWithWebsiteIndex;

    /**
     * Search a website.
     *
     * @param Request $request the request
     *
     * @return JsonResponse the JSON response
     */
    public function search(Request $request): JsonResponse
    {
        if (auth()->user()->isAn(UserRole::SUPER_ADMIN)) {
            $publicAdministration = $request->input('p');
        } else {
            $publicAdministration = current_public_administration()->ipa_code;
        }
        if (isset($request->q)) {
            $result = $this->searchWebsite(
                $request->q,
                $publicAdministration
            );
        }

        return response()->json($result ?? []);
    }
}
