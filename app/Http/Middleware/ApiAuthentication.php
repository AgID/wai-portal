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

        if (null === $consumerId) {
            return response()->json($this->jsonError(), 403);
        }
        $keys = new Key();
        $selectKey = $keys->getKeyFromConsumerId($consumerId);
        $publicAdministration = $selectKey->publicAdministration()->first();

        $request->attributes->add(['publicAdministration' => $publicAdministration]);

        $website = $request->route()->parameter('website');

        if (null !== $website) {
            if (null !== $request->header('X-Consumer-Custom-Id')) {
                $keyCustomId = $request->header('X-Consumer-Custom-Id');
                $allowedId = explode(',', $keyCustomId);
                if (!in_array($website->id, $allowedId)) {
                    return response()->json($this->jsonError(), 403);
                }
            } else {
                return response()->json($this->jsonError(), 403);
            }
        }

        return $next($request);
    }

    protected function jsonError()
    {
        return [
            'title' => 'insufficient permission',
            'message' => 'You\'re not allowed to carry out this action',
            'type' => 'insufficient_permission',
        ];
    }
}
