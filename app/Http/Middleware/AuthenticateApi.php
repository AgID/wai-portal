<?php

namespace App\Http\Middleware;

use App\Models\Credential;
use App\Models\User;
use App\Traits\SendsResponse;
use Closure;

class AuthenticateApi
{
    use SendsResponse;

    /**
     * Check whether the session has a tenant selected for the current request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $consumerId = $request->header('X-Consumer-Id');
        $customId = $request->header('X-Consumer-Custom-Id');

        if (null === $customId || null === $consumerId) {
            return response()->json(['error' => '500 Internal Server Error'], 500);
        }

        $customId = json_decode($customId);
        $credentialType = '';

        if (property_exists($customId, 'type')) {
            $credentialType = $customId->type;
        }

        if ('admin' !== $credentialType) {
            return response()->json($this->jsonError(2), 403);
        }

        $currentCredential = Credential::getCredentialFromConsumerId($consumerId);
        $publicAdministration = $currentCredential->publicAdministration->first();

        $requestApiParams = ['publicAdministrationFromToken' => $publicAdministration];

        if ($request->fn) {
            $user = User::findNotSuperAdminByFiscalNumber($request->fn);
            if (!$user || !$user->publicAdministrationsWithSuspended->contains($publicAdministration)) {
                return $this->notFoundResponse(User::class);
            }
            $requestApiParams['userFromFiscalNumber'] = $user;
        }

        $request->merge($requestApiParams);

        // use english for api responses
        app()->setLocale('en');

        return $next($request);
    }

    /**
     * Return a JSON error
     *
     * @param int $code The error code
     * @return void
     */
    protected function jsonError(int $code)
    {
        return [
            'title' => 'insufficient permissions',
            'status' => 403,
            'code' => $code,
        ];
    }
}
