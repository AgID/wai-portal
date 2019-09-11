<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Website and user responses.
 */
trait SendsResponse
{
    /**
     * Returns a success response for the specified user.
     *
     * @param User $user the user
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function userResponse(User $user)
    {
        return request()->expectsJson()
            ? response()->json([
                'result' => 'ok',
                'id' => $user->uuid,
                'user_name' => e($user->full_name),
                'status' => $user->status->key,
                'status_description' => $user->status->description,
                'trashed' => $user->trashed(),
            ])
            : back()->withNotification([
                'title' => __('utente modificato'),
                'message' => $user->trashed()
                    ? __("L'utente <strong>:user</strong> è stato eliminato.", ['user' => e($user->full_name)])
                    : implode(' ', [
                        __("L'utente <strong>:user</strong> è stato modificato correttamente.\n", ['user' => e($user->full_name)]),
                        __("Stato dell'utente:"),
                        '<span class="badge user-status ' . strtolower($user->status->key) . '">' . strtoupper($user->status->description) . '</span>.',
                    ]),
                'status' => 'info',
                'icon' => 'it-info-circle',
            ]);
    }

    /**
     * Returns a success response for the specified website.
     *
     * @param Website $website the website
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function websiteResponse(Website $website)
    {
        return request()->expectsJson()
            ? response()->json([
                'result' => 'ok',
                'id' => $website->slug,
                'website_name' => e($website->name),
                'status' => $website->status->key,
                'status_description' => $website->status->description,
                'trashed' => $website->trashed(),
            ])
            : back()->withNotification([
                'title' => __('sito web modificato'),
                'message' => $website->trashed()
                    ? __('Il sito web <strong>:website</strong> è stato eliminato.', ['website' => e($website->name)])
                    : implode(' ', [
                        __('Il sito web <strong>:website</strong> è stato modificato correttamente.\n', ['website' => e($website->name)]),
                        __('Stato del sito web:'),
                        '<span class="badge website-status ' . strtolower($website->status->key) . '">' . strtoupper($website->status->description) . '</span>.',
                    ]),
                'status' => 'info',
                'icon' => 'it-info-circle',
            ]);
    }

    /**
     * Returns an not modified response.
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function notModifiedResponse()
    {
        return request()->expectsJson()
            ? response()->json(null, 304)
            : back()->withNotification([
                'title' => __('operazione non effettuata'),
                'message' => __("L'azione richiesta risulta essere già stata effettuata."),
                'status' => 'info',
                'icon' => 'it-info-circle',
            ]);
    }

    /**
     * Returns an error response with the specified parameters.
     *
     * @param string $message the error message
     * @param string $code the error code
     * @param int $httpStatusCode the http status code
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function errorResponse(string $message, string $code, int $httpStatusCode)
    {
        return request()->expectsJson()
            ? response()->json([
                'result' => 'error',
                'message' => $message,
                'code' => $code,
            ], $httpStatusCode)
            : back()->withNotification([
                'title' => __('errore'),
                'message' => implode(' ', [
                    __('Si è verificato un errore relativamente alla tua richiesta.\n'),
                    __('Puoi riprovare più tardi o <a href=":contacts">contattare il supporto tecnico</a>.', ['contacts' => route('contacts')]),
                ]),
                'status' => 'info',
                'icon' => 'it-info-circle',
            ]);
    }
}
