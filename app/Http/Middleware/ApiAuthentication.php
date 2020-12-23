<?php

namespace App\Http\Middleware;

use App\Models\Key;
use Closure;
use Illuminate\Session\Middleware\StartSession;

class ApiAuthentication extends StartSession
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

        if (null === $customId) {
            return response()->json($this->jsonError(1), 403);
        }

        $customId = json_decode($customId);
        $keyType = $keySites = '';

        if (property_exists($customId, 'type') && property_exists($customId, 'siteId')) {
            $keyType = $customId->type;
            $keySites = $customId->siteId;
        }

        if (null === $consumerId || 'admin' !== $keyType || !is_array($keySites)) {
            print_r([$consumerId, $keyType, $keySites]);

            return response()->json($this->jsonError(2), 403);
        }

        $keys = new Key();
        $selectKey = $keys->getKeyFromConsumerId($consumerId);
        $publicAdministration = $selectKey->publicAdministration()->first();

        $request->attributes->add(['publicAdministration' => $publicAdministration]);

        $website = $request->route()->parameter('website');

        if (null !== $website) {
            $column = array_column($keySites, 'id');
            if (!in_array($website->id, $column)) {
                return response()->json($this->jsonError(3), 401);
            }
        }

        return $next($request);
    }

    protected function jsonError($code)
    {
        return [
            'title' => 'insufficient permission err. ' . $code,
            'message' => 'You\'re not allowed to carry out this action',
            'type' => 'insufficient_permission',
        ];
    }
}
