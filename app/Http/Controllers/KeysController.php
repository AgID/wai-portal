<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Http\Requests\StoreKeysRequest;
use App\Http\Requests\UpdateKeyRequest;
use App\Models\Key;
use App\Models\PublicAdministration;
use App\Models\Website;
use App\Traits\HasRoleAwareUrls;
use App\Transformers\KeysTransformer;
use App\Transformers\WebsitesPermissionsTransformer;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class KeysController extends Controller
{
    use HasRoleAwareUrls;
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

    public function create(Request $request, PublicAdministration $publicAdministration)
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

    public function show(Request $request, Key $key, PublicAdministration $publicAdministration): View
    {
        $user = auth()->user();
        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $client = $this->clientService->getClient($key->consumer_id);

        $roleAwareUrls = $this->getRoleAwareUrlArray([
            'keyEditUrl' => 'api-key.edit',
        ], [
            'key' => $key,
        ], $currentPublicAdministration);

        $websitesPermissionsDatatableSource = $this->getRoleAwareUrl(
            'api-key.websites.permissions',
            ['key' => $key],
            $currentPublicAdministration
        );

        $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource, true);

        $keyData = [
            //aggiungere eccezzione se non esiste client['data']
            'client' => $client['data'][0],
        ];

        return view('pages.keys.show')
            ->with(compact('key'))
            ->with($keyData)
            ->with($websitesPermissionsDatatable)
            ->with($roleAwareUrls);
    }

    public function edit(Request $request, Key $key, PublicAdministration $publicAdministration)
    {
        $user = auth()->user();
        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $updateUrl = $this->getRoleAwareUrl('api-key.update', [
            'key' => $key,
        ], $currentPublicAdministration);

        $consumer = $this->clientService->getConsumer($key->consumer_id);
        $customId = explode(',', $consumer['custom_id']);

        $websitesPermissionsDatatableSource = $this->getRoleAwareUrl(
            'api-key.websites.permissions.make',
            [
                'key' => $key,
                'oldKeyPermissions' => $customId,
            ],
            $currentPublicAdministration
        );

        $websitesPermissionsDatatable = $this->getDatatableWebsitesPermissionsParams($websitesPermissionsDatatableSource);

        return view('pages.keys.edit')->with(compact('key', 'updateUrl'))->with($websitesPermissionsDatatable);
    }

    public function update(UpdateKeyRequest $request, Key $key)
    {
        $validatedData = $request->validated();

        $permissionsArray = array_keys($validatedData['permissions']);
        $permissions = implode(',', $permissionsArray);

        $key->fill([
            'client_name' => $validatedData['key_name'],
        ]);
        $key->save();

        $clientJSON = $this->clientService->updateClient(
            $key->consumer_id,
            [
                'username' => $validatedData['key_name'],
                'custom_id' => $permissions,
            ]
        );

        return redirect()->route('api-key.index')->withModal([
            'title' => __('La chiave ' . $validatedData['key_name'] . ' è stata modificata con successo!'),
            'icon' => 'it-check-circle',
            'message' => __('Adesso puoi utilizzare il tuo client_id e client_secret per ottenere il token OAuth2 e usare le API'),
        ]);
    }

    public function delete(Request $request)
    {
        //aggiungere il delete
    }

    public function dataJson(PublicAdministration $publicAdministration)
    {
        $user = auth()->user();
        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $data = $currentPublicAdministration->keys()->get();

        return DataTables::of($data)
            ->setTransformer(new KeysTransformer()) //Key Transformer
            ->make(true);
    }

    public function store(StoreKeysRequest $request, PublicAdministration $publicAdministration)
    {
        $validatedData = $request->validated();
        $permissionsArray = array_keys($validatedData['permissions']);
        $permissions = implode(',', $permissionsArray);

        $user = auth()->user();

        $currentPublicAdministration = $user->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        $clientJSON = $this->clientService->makeConsumer($validatedData['key_name'], $permissions);

        $client = Key::create([
            'client_name' => $validatedData['key_name'],
            'public_administration_id' => $currentPublicAdministration->id,
            'consumer_id' => $clientJSON['consumer']['id'],
        ]);

        return redirect()->route('api-key.index')->withModal([
            'title' => __('La chiave è stata inserita!'),
            'icon' => 'it-check-circle',
            'message' => __('Adesso puoi utilizzare il tuo client_id e client_secret per ottenere il token OAuth2 e usare le API'),
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
        $customId = explode(',', $consumer['custom_id']);
        $websites = Website::whereIn('id', $customId)->get();

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
        $websites = Website::all();

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
