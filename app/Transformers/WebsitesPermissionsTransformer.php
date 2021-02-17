<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Models\Website;
use League\Fractal\TransformerAbstract;
use Silber\Bouncer\BouncerFacade as Bouncer;

/**
 * Website permissions transformer.
 */
class WebsitesPermissionsTransformer extends TransformerAbstract
{
    /**
     * Transform the website permission for datatable.
     *
     * @param Website $website the website
     *
     * @return array the response
     */
    public function transform(Website $website): array
    {
        $publicAdministration = request()->route('publicAdministration', current_public_administration());
        $websiteUrlLink = array_key_exists('scheme', parse_url($website->url))
            ? e($website->url)
            : 'http://' . e($website->url);

        return Bouncer::scope()->onceTo($publicAdministration->id, function () use ($website, $websiteUrlLink) {
            $user = request()->route('user');
            $readOnly = request()->has('readOnly');
            $editCredentialPermissions = request()->has('editCredentialPermissions');
            $oldPermissions = request()->query('oldPermissions');
            $oldCredentialPermission = request()->query('oldCredentialPermissions');
            $canRead = !is_array($oldPermissions) && optional($user)->can(UserPermission::READ_ANALYTICS, $website);
            $canManage = !is_array($oldPermissions) && optional($user)->can(UserPermission::MANAGE_ANALYTICS, $website);

            $data = [
                'website_name' => [
                    'display' => implode('', [
                        '<span>',
                        '<strong>' . e($website->name) . '</strong>',
                        '<br>',
                        '<small><a href="' . $websiteUrlLink . '" target="_blank">' . e($website->url) . '</a></small>',
                        '</span>',
                    ]),
                    'raw' => e($website->name),
                ],
                'type' => $website->type->description,
                'status' => [
                    'display' => '<span class="badge website-status ' . strtolower($website->status->key) . '">' . strtoupper($website->status->description) . '</span>',
                    'raw' => $website->status->description,
                ],
            ];

            if ($editCredentialPermissions) {
                if ($readOnly) {
                    $data['icons'] = [
                        [
                            'icon' => $this->getCredentialPermission($website->analytics_id, $oldCredentialPermission, 'R') ? 'it-check-circle' : 'it-close-circle',
                            'color' => $this->getCredentialPermission($website->analytics_id, $oldCredentialPermission, 'R') ? 'success' : 'danger',
                            'label' => 'gestione',
                        ],
                        [
                            'icon' => $this->getCredentialPermission($website->analytics_id, $oldCredentialPermission, 'W') ? 'it-check-circle' : 'it-close-circle',
                            'color' => $this->getCredentialPermission($website->analytics_id, $oldCredentialPermission, 'W') ? 'success' : 'danger',
                            'label' => 'lettura',
                        ],
                    ];
                } else {
                    $data['toggles'] = [
                        [
                            'name' => 'permissions[' . $website->analytics_id . '][]',
                            'value' => 'R',
                            'label' => 'lettura',
                            'checked' => $this->getCredentialPermission($website->analytics_id, $oldCredentialPermission, 'R'),
                            'dataAttributes' => [
                                'entity' => $website->analytics_id,
                            ],
                        ],
                        [
                            'name' => 'permissions[' . $website->analytics_id . '][]',
                            'value' => 'W',
                            'label' => 'gestione',
                            'checked' => $this->getCredentialPermission($website->analytics_id, $oldCredentialPermission, 'W'),
                            'dataAttributes' => [
                                'entity' => $website->analytics_id,
                            ],
                        ],
                    ];
                }
            } else {
                if ($readOnly) {
                    $data['icons'] = [
                        [
                            'icon' => $canRead ? 'it-check-circle' : 'it-close-circle',
                            'color' => $canRead ? 'success' : 'danger',
                            'label' => UserPermission::getDescription(UserPermission::READ_ANALYTICS),
                        ],
                        [
                            'icon' => $canManage ? 'it-check-circle' : 'it-close-circle',
                            'color' => $canManage ? 'success' : 'danger',
                            'label' => UserPermission::getDescription(UserPermission::MANAGE_ANALYTICS),
                        ],
                    ];
                } else {
                    $data['toggles'] = [
                        [
                            'name' => 'permissions[' . $website->id . '][]',
                            'value' => UserPermission::READ_ANALYTICS,
                            'label' => UserPermission::getDescription(UserPermission::READ_ANALYTICS),
                            'checked' => in_array(UserPermission::READ_ANALYTICS, $oldPermissions[$website->id] ?? []) || $canRead,
                            'dataAttributes' => [
                                'entity' => $website->id,
                            ],
                        ],
                        [
                            'name' => 'permissions[' . $website->id . '][]',
                            'value' => UserPermission::MANAGE_ANALYTICS,
                            'label' => UserPermission::getDescription(UserPermission::MANAGE_ANALYTICS),
                            'checked' => in_array(UserPermission::MANAGE_ANALYTICS, $oldPermissions[$website->id] ?? []) || $canManage,
                            'dataAttributes' => [
                                'entity' => $website->id,
                            ],
                        ],
                    ];
                }
            }

            return $data;
        });
    }

    protected function getCredentialPermission(int $id, ?array $permissions, string $credential): bool
    {
        if (null === $permissions) {
            return false;
        }

        $column = array_column($permissions, 'id');

        $found_credential = array_search($id, $column);
        $site = $permissions[$found_credential];
        $permission = $site['permission'];

        return str_contains($permission, $credential);
    }
}
