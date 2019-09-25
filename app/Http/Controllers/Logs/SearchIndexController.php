<?php

namespace App\Http\Controllers\Logs;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Traits\InteractsWithRedisIndex;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Redis index controller.
 */
class SearchIndexController extends Controller
{
    use InteractsWithRedisIndex;

    /**
     * Search a user.
     *
     * @param Request $request the request
     *
     * @return JsonResponse the JSON response
     */
    public function searchUser(Request $request): JsonResponse
    {
        if ($request->user()->isAn(UserRole::SUPER_ADMIN)) {
            $publicAdministration = $request->input('public_administration');
        } else {
            $publicAdministration = current_public_administration()->ipa_code;
        }
        if ($request->has('q')) {
            $result = $this->searchUsersIndex(
                $request->q,
                $publicAdministration
            );
        }

        return response()->json($result ?? []);
    }

    /**
     * Search a website.
     *
     * @param Request $request the request
     *
     * @return JsonResponse the JSON response
     */
    public function searchWebsite(Request $request): JsonResponse
    {
        if ($request->user()->isAn(UserRole::SUPER_ADMIN)) {
            $publicAdministration = $request->input('public_administration');
        } else {
            $publicAdministration = current_public_administration()->ipa_code;
        }

        if ($request->has('q')) {
            $result = $this->searchWebsitesIndex($request->q, $publicAdministration);
        }

        return response()->json($result ?? []);
    }
}
