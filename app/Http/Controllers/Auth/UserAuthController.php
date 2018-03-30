<?php

namespace App\Http\Controllers\Auth;

use App\Jobs\ClearPasswordResetToken;
use App\Jobs\SendPasswordResetEmail;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthController extends Controller
{
    /**
     * Show the profile page.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('auth.profile');
    }
}
