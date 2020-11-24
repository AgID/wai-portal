<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SwaggerController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.swagger');
    }
}
