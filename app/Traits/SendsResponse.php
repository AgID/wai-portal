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
                    ? __("L'utente :user è stato eliminato.", ['user' => '<strong>' . e($user->full_name) . '</strong>'])
                    : implode("\n", [
                        __("L'utente :user è stato aggiornato.", ['user' => '<strong>' . e($user->full_name) . '</strong>']),
                        __("Stato dell'utente: :status", [
                            'status' => '<span class="badge user-status ' . strtolower($user->status->key) . '">' . strtoupper($user->status->description) . '</span>.',
                        ]),
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
                    ? __('Il sito web :website è stato eliminato.', ['website' => '<strong>' . e($website->name) . '</strong>'])
                    : implode("\n", [
                        __('Il sito web :website è stato aggiornato.', ['website' => '<strong>' . e($website->name) . '</strong>']),
                        __('Stato del sito web: :status', [
                            'status' => '<span class="badge website-status ' . strtolower($website->status->key) . '">' . strtoupper($website->status->description) . '</span>.',
                        ]),
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
                'message' => __('La richiesta non ha determinato cambiamenti nello stato.'),
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
                'title' => __('errore del server'),
                'message' => implode("\n", [
                    __('Si è verificato un errore relativamente alla tua richiesta.'),
                    __('Puoi riprovare più tardi o :contact_support.', ['contact_support' => '<a href="' . route('contacts') . '">' . __('contattare il supporto tecnico') . '</a>']),
                ]),
                'status' => 'error',
                'icon' => 'it-close-circle',
            ]);
    }
}
