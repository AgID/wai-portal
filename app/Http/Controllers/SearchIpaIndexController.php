<?php

namespace App\Http\Controllers;

use App\Traits\InteractsWithRedisIndex;
use Illuminate\Http\Request;

class SearchIpaIndexController extends Controller
{
    use InteractsWithRedisIndex;

    /**
     * Return elements found in IPA index.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if (isset($request->q)) {
            $result = $this->searchPublicAdministration($request->q);
        }

        return response()->json($result ?? []);
    }
}
