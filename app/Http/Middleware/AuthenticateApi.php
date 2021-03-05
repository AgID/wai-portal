<?php

namespace App\Http\Middleware;

use App\Models\Credential;
use Closure;

class AuthenticateApi
{
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
        $request->merge(['publicAdministrationFromToken' => $publicAdministration]);

        // use english for api responses
        app()->setLocale('en');

        return $next($request);
    }

    protected function jsonError($code)
    {
        return [
            'title' => 'insufficient permissions',
            'status' => 403,
            'code' => $code,
        ];
    }
}
