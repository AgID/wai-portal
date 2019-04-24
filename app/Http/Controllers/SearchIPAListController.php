<?php

namespace App\Http\Controllers;

use App\Traits\InteractsWithIPAIndex;
use Illuminate\Http\Request;

class SearchIPAListController extends Controller
{
    use InteractsWithIPAIndex;

    /**
     * Return elements found in IPA list.
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
