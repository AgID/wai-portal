<?php

use App\Models\User;

use Italia\SPIDAuth\SPIDUser;

/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
|
| These route are valid only for testing and are loaded only in "testing"
| environment.
|
|                           **SECURITY ALERT**
| Never use these routes in production as they bypass every security check.
|
*/

Route::get('/_test/_session_put/{key}/{value}', function ($key, $value) {
    $value = json_decode($value) ?: $value;
    session([$key => $value]);
});

Route::get('/_test/_session_get/{key}', function ($key) {
    return session($key);
});

Route::get('/_test/_inject_spid_session', function () {
    $SPIDUser = new SPIDUser([
        "mobilePhone" => ["+390000000000"],
        "familyName" => ["Cognome"],
        "name" => ["Nome"],
        "spidCode" => ["TEST1234567890"],
        "fiscalNumber" => ["FSCLNB17A01H501X"],
        "email" => ["mail@example.com"]
    ]);
    session(['spid_sessionIndex' => 'fake-session-index']);
    session(['spid_user' => $SPIDUser]);
});

Route::get('/_test/_assign_role/{userId}/{role}', function ($userId, $role) {
    User::find($userId)->assign($role);
});

Route::get('/_test/_get_user_verification_token/{userId}', function ($userId) {
    return User::find($userId)->verificationToken->token;
});