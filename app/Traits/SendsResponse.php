<?php

namespace App\Traits;

use App\Enums\UserStatus;
use App\Models\Key;
use App\Models\PublicAdministration;
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
    public function userResponse(User $user, ?PublicAdministration $publicAdministration = null)
    {
        $userStatus = $user->status;
        $userTrashed = $user->trashed() ? $user->trashed() : false;
        if ($publicAdministration) {
            if ($user->publicAdministrationsWithSuspended()->where('public_administration_id', $publicAdministration->id)->get()->isNotEmpty()) {
                $userStatus = $user->getStatusforPublicAdministration($publicAdministration);
            } else {
                $userTrashed = true;
            }
        }

        return request()->expectsJson()
            ? response()->json([
                'result' => 'ok',
                'id' => $user->uuid,
                'user_name' => e($user->full_name),
                'status' => $userStatus->key,
                'status_description' => $userStatus->description,
                'trashed' => $userTrashed,
                'administration' => $publicAdministration ? $publicAdministration->name : null,
            ])
            : back()->withNotification([
                'title' => __('utente modificato'),
                'message' => $userTrashed
                    ? ($publicAdministration
                        ? __("L'utente :user è stato eliminato da :pa.", ['user' => '<strong>' . e($user->full_name) . '</strong>', 'pa' => '<strong>' . e($publicAdministration->name) . '</strong>'])
                        : __("L'utente :user è stato eliminato.", ['user' => '<strong>' . e($user->full_name) . '</strong>']))
                    : implode("\n", [
                        __("L'utente :user è stato aggiornato.", ['user' => '<strong>' . e($user->full_name) . '</strong>']),
                        __("Stato dell'utente: :status", [
                            'status' => '<span class="badge user-status ' . strtolower($userStatus->key) . '">' . strtoupper($userStatus->description) . '</span>.',
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
     * Returns a success response for the specified public administration.
     *
     * @param PublicAdministration $website the website
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function publicAdministrationResponse(PublicAdministration $publicAdministration)
    {
        return request()->expectsJson()
            ? response()->json([
                'result' => 'ok',
                'id' => $publicAdministration->id,
                'name' => e($publicAdministration->name),
                'status' => $publicAdministration->status->key,
                'status_description' => $publicAdministration->status->description,
                'ipa_code' => $publicAdministration->ipa_code,
            ])
            : back()->withNotification([
                'title' => __('Pubblica amministrazione modificata'),
                'message' => __("L'invito alla pubblica amministrazione :pa è stato confermato.", ['pa' => '<strong>' . e($publicAdministration->name) . '</strong>']),
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
     * Returns a success response for the specified key.
     *
     * @param Key $key the key
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function keyResponse(Key $key)
    {
        return request()->expectsJson()
            ? response()->json([
                'result' => 'ok',
                'id' => $key->consumer_id,
                'key_name' => e($key->client_name),
                'status' => 200,
            ])
            : back()->withNotification([
                'title' => __('Chiave modificata'),
                'message' => __('Il sito web :website è stato eliminato.', ['website' => '<strong>' . e($key->client_name) . '</strong>']),
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
