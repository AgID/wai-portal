<?php

use Italia\SPIDAuth\SPIDUser;

/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
|
| These route are valid only for testing and are loaded only in
| non-produtcion environments.
|
|                           **SECURITY ALERT**
| Never use these routes in production as they bypass every security check.
|
*/

Route::get('/_test/_inject_spid_session', function () {
    $SPIDUser = new SPIDUser([
        'mobilePhone' => ['+390000000000'],
        'familyName' => ['Cognome'],
        'name' => ['Nome'],
        'spidCode' => ['TEST1234567890'],
        'fiscalNumber' => ['FSCLNB17A01H501X'],
        'email' => ['mail@example.com'],
    ]);
    session(['spid_sessionIndex' => 'fake-session-index']);
    session(['spid_user' => $SPIDUser]);
});
