<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserLogin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\GetsLocalizedYamlContent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;

/**
 * User registration controller.
 */
class RegisterController extends Controller
{
    use GetsLocalizedYamlContent;

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View the view
     */
    public function showRegistrationForm(): View
    {
        $tos = $this->getLocalizedYamlContent('tos');

        return view('auth.register')->with(compact('tos'));
    }

    /**
     * Perform registration and login.
     *
     * @param Request $request the incoming request
     *
     * @throws \Exception if unable to generate user UUID
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function register(Request $request): RedirectResponse
    {
        $input = $request->all();
        $validatedData = validator($input, [
            'email' => 'required|email:rfc,dns|max:75',
            'accept_terms' => 'required',
        ])->after(function ($validator) use ($input) {
            if (array_key_exists('email', $input) && User::where('email', $input['email'])->whereDoesntHave('roles', function ($query) {
                $query->where('name', UserRole::SUPER_ADMIN);
            })->get()->isNotEmpty()) {
                $validator->errors()->add('email', __('validation.unique', ['attribute' => __('validation.attributes.email')]));
            }
        })->validate();

        $SPIDUser = session()->get('spid_user');
        $user = User::create([
            'spid_code' => $SPIDUser->spidCode,
            'name' => $SPIDUser->name,
            'family_name' => $SPIDUser->familyName,
            'fiscal_number' => $SPIDUser->fiscalNumber,
            'uuid' => Uuid::uuid4()->toString(),
            'email' => $validatedData['email'],
            'status' => UserStatus::INACTIVE,
            'last_access_at' => Date::now(),
        ]);

        event(new Registered($user));

        $user->assign(UserRole::REGISTERED);
        auth()->login($user);

        event(new UserLogin($user));

        return redirect()->route('verification.notice');
    }
}
