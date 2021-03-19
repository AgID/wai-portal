<?php

namespace App\Transformers;

use App\Enums\CredentialPermission;
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
            $oldPermissions = request()->query('oldPermissions');
            $canRead = !is_array($oldPermissions) && optional($user)->can(UserPermission::READ_ANALYTICS, $website);
            $canManage = !is_array($oldPermissions) && optional($user)->can(UserPermission::MANAGE_ANALYTICS, $website);

            $isCredentialPermissionsData = 'api-credentials.websites.permissions' === request()->route()->getName();
            $credentialPermissions = optional(request()->route('credential'))->permissions;
            $oldCredentialPermission = request()->query('oldCredentialPermissions');
            $canReadCredential = !is_array($oldCredentialPermission)
                && $this->getCredentialPermission($website->analytics_id, $credentialPermissions, CredentialPermission::READ);
            $canWriteCredential = !is_array($oldCredentialPermission)
                && $this->getCredentialPermission($website->analytics_id, $credentialPermissions, CredentialPermission::WRITE);

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

            if ($isCredentialPermissionsData) {
                if ($readOnly) {
                    $data['icons'] = [
                        [
                            'icon' => $canReadCredential ? 'it-check-circle' : 'it-close-circle',
                            'color' => $canReadCredential ? 'success' : 'danger',
                            'label' => CredentialPermission::getDescription(CredentialPermission::READ),
                        ],
                        [
                            'icon' => $canWriteCredential ? 'it-check-circle' : 'it-close-circle',
                            'color' => $canWriteCredential ? 'success' : 'danger',
                            'label' => CredentialPermission::getDescription(CredentialPermission::WRITE),
                        ],
                    ];
                } else {
                    $data['toggles'] = [
                        [
                            'name' => 'permissions[' . $website->analytics_id . '][]',
                            'value' => CredentialPermission::READ,
                            'label' => CredentialPermission::getDescription(CredentialPermission::READ),
                            'checked' => in_array(CredentialPermission::READ, $oldCredentialPermission[$website->analytics_id] ?? []) || $canReadCredential,
                            'dataAttributes' => [
                                'entity' => $website->analytics_id,
                            ],
                        ],
                        [
                            'name' => 'permissions[' . $website->analytics_id . '][]',
                            'value' => CredentialPermission::WRITE,
                            'label' => CredentialPermission::getDescription(CredentialPermission::WRITE),
                            'checked' => in_array(CredentialPermission::WRITE, $oldCredentialPermission[$website->analytics_id] ?? []) || $canWriteCredential,
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

    /**
     * Get website's credential permissions
     *
     * @param integer $websiteId The website ID
     * @param array|null $credentialPermissions The Credential permissions
     * @param string $permissionType The permission Type
     * @return boolean Whether has or doesn't have a permission
     */
    protected function getCredentialPermission(int $websiteId, ?array $credentialPermissions, string $permissionType): bool
    {
        if (!is_array($credentialPermissions)) {
            return false;
        }

        $websiteIds = array_column($credentialPermissions, 'id');
        $websitePermissionIndex = array_search($websiteId, $websiteIds);

        if (false === $websitePermissionIndex) {
            return false;
        }

        $websitePermissions = $credentialPermissions[$websitePermissionIndex];

        return array_key_exists('permission', $websitePermissions) && str_contains($websitePermissions['permission'], $permissionType);
    }
}
