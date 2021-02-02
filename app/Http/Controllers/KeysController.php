<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Exceptions\InvalidKeyException;
use App\Http\Requests\StoreKeysRequest;
use App\Http\Requests\UpdateKeyRequest;
use App\Models\Key;
use App\Models\PublicAdministration;
use App\Models\Website;
use App\Traits\HasRoleAwareUrls;
use App\Traits\SendsResponse;
use App\Transformers\KeysTransformer;
use App\Transformers\WebsitesPermissionsTransformer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class KeysController extends Controller
{
    use HasRoleAwareUrls;
    use SendsResponse;

    protected $clientService;

    public function __construct()
    {
        $this->clientService = app()->make('kong-client-service');
    }

    /**
     * Display the key list.
     *
     * @param PublicAdministration $publicAdministration the public administration the websites belong to
     *
     * @return View the view
     */
    public function index(Request $request, PublicAdministration $publicAdministration)
    {
        $keysDatatable = [
            'columns' => [
                ['data' => 'client_name', 'name' => __('Nome della chiave'), 'className' => 'text-wrap'],
                ['data' => 'consumer_id', 'name' => __('Consumer ID')],
                ['data' => 'added_at', 'name' => __('aggiunto il')],
                ['data' => 'icons', 'name' => '', 'orderable' => false],
                ['data' => 'buttons', 'name' => '', 'orderable' => false],
            ],
            'source' => $this->getRoleAwareUrl('api-key.data.json', [], $publicAdministration),
            'caption' => __('elenco delle chiavi presenti su :app', ['app' => config('app.name')]),
            'columnsOrder' => [['added_at', 'asc'], ['client_name', 'asc']],
        ];

        $roleAwareUrls = $this->getRoleAwareUrlArray([
            'newKeyUrl' => 'api-key.create',
        ], [], $publicAdministration);

        return view('pages.keys.index')
            ->with($roleAwareUrls)
            ->with($keysDatatable);
    }

    /**
     * Create a new Key.
     *
     * @param Request $request the request
     * @param PublicAdministration $publicAdministration the keys belong to
     *
     * @return View the view
     */
    public function create(Request $request, PublicAdministration $publicAdministration): View
    {
        $user = auth()->user();
        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $keysStoreUrl = $this->getRoleAwareUrl('api-key.store', [], $currentPublicAdministration);

        $websitesPermissionsDatatableSource = $this->getRoleAwareUrl(
            'api-key.websites.permissions.make',
            ['key' => null],
            $currentPublicAdministration
        );

        $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource);

        return view('pages.keys.add')->with(compact('keysStoreUrl'))->with($websitesPermissionsDatatable);
    }

    /**
     * Display the key details.
     *
     * @param Request $request the request
     * @param Key $key the key
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     *
     * @return View|Redirect the view or redirect error
     */
    public function show(Request $request, Key $key, PublicAdministration $publicAdministration)
    {
        $user = auth()->user();

        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        if ($currentPublicAdministration->id === $key->public_administration_id) {
            $client = $this->clientService->getClient($key->consumer_id);

            $consumer = $this->clientService->getConsumer($key->consumer_id);
            $customId = (array) json_decode($consumer['custom_id']);
            $conumerType = $customId['type'];

            $sitesIdArray = is_array($customId['siteId'])
                ? $customId['siteId']
                : [];

            $roleAwareUrls = $this->getRoleAwareUrlArray([
                'keyEditUrl' => 'api-key.edit',
            ], [
                'key' => $key,
            ], $currentPublicAdministration);

            $websitesPermissionsDatatableSource = $this->getRoleAwareUrl(
                'api-key.websites.permissions',
                ['key' => $key, 'oldKeyPermissions' => $sitesIdArray],
                $currentPublicAdministration
            );

            $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource, true);

            $keyData = [
                'client' => $client,
                'type' => $conumerType,
            ];

            return view('pages.keys.show')
                ->with(compact('key'))
                ->with($keyData)
                ->with($websitesPermissionsDatatable)
                ->with($roleAwareUrls);
        }

        return redirect()->home()->withNotification([
            'title' => __('Non è possibile visualizzare questa chiave'),
            'message' => "La chiave appartiene ad un'altra pubblica amministrazione",
            'status' => 'error',
            'icon' => 'it-close-circle',
        ]);
    }

    public function showJson(Key $key, PublicAdministration $publicAdministration)
    {
        $user = auth()->user();

        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        if ($currentPublicAdministration->id === $key->public_administration_id) {
            $keyData = $this->clientService->getClient($key->consumer_id);

            return response()->json([
                'key' => $keyData,
            ], 200);
        }

        return response()->json([
            'Error' => true,
            'Message' => "La chiave appartiene ad un'altra pubblica amministrazione",
        ], 403);
    }

    /**
     * Edit key view page.
     *
     * @param Request $request The request
     * @param Key $key The key
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     *
     * @return View the view
     */
    public function edit(Request $request, Key $key, PublicAdministration $publicAdministration): View
    {
        $user = auth()->user();
        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $updateUrl = $this->getRoleAwareUrl('api-key.update', [
            'key' => $key,
        ], $currentPublicAdministration);

        $consumer = $this->clientService->getConsumer($key->consumer_id);
        $customId = json_decode($consumer['custom_id']);
        $sitesIdArray = is_array($customId->siteId)
            ? $customId->siteId
            : [];

        $websitesPermissionsDatatableSource = $this->getRoleAwareUrl(
            'api-key.websites.permissions.make',
            [
                'key' => $key,
                'oldKeyPermissions' => $sitesIdArray,
            ],
            $currentPublicAdministration
        );

        $keyData = [
            'type' => $customId->type,
        ];

        $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource);

        return view('pages.keys.edit')
            ->with(compact('key', 'updateUrl'))
            ->with($websitesPermissionsDatatable)
            ->with($keyData);
    }

    /**
     * Update the keys.
     *
     * @param UpdateKeyRequest $request The request
     * @param Key $key The key
     *
     * @return RedirectResponse
     */
    public function update(UpdateKeyRequest $request, Key $key): RedirectResponse
    {
        $validatedData = $request->validated();

        $permissions = [];
        if (array_key_exists('permissions', $validatedData)) {
            foreach ($validatedData['permissions'] as $keyId => $permission) {
                array_push($permissions, ['id' => $keyId, 'permission' => implode('', $permission)]);
            }
        }

        $key->fill([
            'client_name' => $validatedData['key_name'],
        ]);
        $key->save();

        $clientJSON = $this->clientService->updateClient(
            $key->consumer_id,
            [
                'username' => $validatedData['key_name'],
                'custom_id' => json_encode(['type' => $validatedData['type'], 'siteId' => $permissions]),
            ]
        );

        return redirect()->route('api-key.index')->withModal([
            'title' => __('modifica credenziale'),
            'icon' => 'it-check-circle',
            'message' => __('La modifica della credenziale :credential è andata a buon fine.', [
                'credential' => '<strong>' . $validatedData['key_name'] . '</strong>'
            ]),
        ]);
    }

    /**
     * Deletes the key.
     *
     * @param Key $key The key
     *
     * @return JsonResponse|RedirectResponse the response in json or http redirect format
     */
    public function delete(Key $key)
    {
        try {
            $this->clientService->deleteConsumer($key->consumer_id);
            $key->delete();

            return $this->keyResponse($key);
        } catch (InvalidKeyException $exception) {
            report($exception);
            $code = $exception->getCode();
            $message = 'Invalid Key';
            $httpStatusCode = 400;
        }

        return $this->errorResponse($message, $code, $httpStatusCode);
    }

    /**
     * Get the Keys data.
     *
     * @param PublicAdministration $publicAdministration the Public Administration to filter keys or null to use current one
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response in JSON format
     */
    public function dataJson(PublicAdministration $publicAdministration)
    {
        $user = auth()->user();
        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $data = $currentPublicAdministration->keys()->get();

        return DataTables::of($data)
            ->setTransformer(new KeysTransformer())
            ->make(true);
    }

    public function store(StoreKeysRequest $request, PublicAdministration $publicAdministration)
    {
        $validatedData = $request->validated();
        $permissions = [];

        foreach ($validatedData['permissions'] as $key => $permission) {
            array_push($permissions, ['id' => $key, 'permission' => implode('', $permission)]);
        }

        $user = auth()->user();

        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $clientJSON = $this->clientService
            ->makeConsumer(
                $validatedData['key_name'],
                json_encode(['type' => $validatedData['type'], 'siteId' => $permissions])
            );

        $client = Key::create([
            'client_name' => $validatedData['key_name'],
            'public_administration_id' => $currentPublicAdministration->id,
            'consumer_id' => $clientJSON['consumer']['id'],
        ]);

        return redirect()->route('api-key.index')->withModal([
            'title' => __('La chiave è stata inserita!'),
            'icon' => 'it-check-circle',
            'message' => __('Adesso puoi utilizzare la tua nuova credenziale e usare le API con il flusso "Client credentials" OAuth2.'),
        ]);
    }

    /**
     * Show the oauth key permissions on websites.
     *
     * @param Website $websites websites associated with the key
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response in JSON format
     */
    public function showDataKeyWebsitesPermissionsJson(Key $key)
    {
        $consumer = $this->clientService->getConsumer($key->consumer_id);
        $customId = json_decode($consumer['custom_id']); //explode(',', $consumer['custom_id']);
        $sitesIdArray = is_array($customId->siteId)
            ? array_map(function ($elem) {
                return $elem->id;
            }, $customId->siteId)
            : [];

        $websites = Website::whereIn('id', $sitesIdArray)->get();

        return DataTables::of($websites)
            ->setTransformer(new WebsitesPermissionsTransformer())
            ->make(true);
    }

    /**
     * Show the oauth key permissions on websites.
     *
     * @param Website $websites websites associated with the key
     *
     * @throws \Exception if unable to initialize the datatable
     *
     * @return mixed the response in JSON format
     */
    public function makeKeyWebsitesPermissionsJson(?Key $key)
    {
        $publicAdministration = current_public_administration();

        $websites = $publicAdministration->websites->all();

        return DataTables::of($websites)
            ->setTransformer(new WebsitesPermissionsTransformer())
            ->make(true);
    }

    /**
     * Get the datatable parameters for websites permission with specified source.
     *
     * @param string $source the source paramater for the websites permission datatable
     * @param bool $readonly wether the datatable is readonly
     *
     * @return array the datatable parameters
     */
    public function getDatatableWebsitesPermissionsParams(string $source, bool $readonly = false): array
    {
        return [
            'datatableOptions' => [
                'searching' => [
                    'label' => __('cerca tra i siti web'),
                ],
                'columnFilters' => [
                    'type' => [
                        'filterLabel' => __('tipologia'),
                    ],
                    'status' => [
                        'filterLabel' => __('stato'),
                    ],
                ],
            ],
            'columns' => [
                ['data' => 'website_name', 'name' => __('nome del sito'), 'className' => 'text-wrap'],
                ['data' => 'type', 'name' => __('tipologia')],
                ['data' => 'status', 'name' => __('stato')],
                [
                    'data' => ($readonly ? 'icons' : 'toggles'),
                    'name' => __('permessi della chiave'),
                    'orderable' => false,
                    'searchable' => false,
                ],
            ],
            'source' => $source . '?&editKeyPermissions' . ($readonly ? '&readOnly' : ''),
            'caption' => __('elenco dei siti web presenti su :app', ['app' => config('app.name')]),
            'columnsOrder' => [['website_name', 'asc']],
        ];
    }
}
