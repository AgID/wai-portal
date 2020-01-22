<?php
/**
 * This file contains the configuration options needed for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

return [
    'sp_entity_id' => env('SPID_SP_ENTITY_ID'),
    'sp_base_url' => env('APP_URL'),
    'sp_service_name' => 'Web Analytics Italia',
    'sp_organization_name' => 'AGID',
    'sp_organization_display_name' => "Agenzia per l'Italia digitale",
    'sp_organization_url' => 'https://www.agid.gov.it',
    'sp_requested_attributes' => [
        'spidCode',
        'name',
        'familyName',
        //'placeOfBirth',
        //'countyOfBirth',
        //'dateOfBirth',
        //'gender',
        'fiscalNumber',
        //'companyName',
        //'registeredOffice',
        //'ivaCode',
        //'idCard',
        //'mobilePhone',
        //'email',
        //'address',
        //'digitalAddress',
        //'expirationDate'
    ],
    'sp_certificate_file' => null,
    'sp_private_key_file' => null,
    'sp_certificate' => env('SPID_SP_CERTIFICATE'),
    'sp_private_key' => env('SPID_SP_PRIVATE_KEY'),
    'sp_spid_level' => 'https://www.spid.gov.it/SpidL1',

    'hide_real_idps' => 'production' !== env('APP_ENV'),
    'expose_sp_metadata' => true,
    'expose_idps_json' => false,
    'test_idp' => env('SPID_TESTENV_ENABLED', false) ? [
        'entityId' => env('SPID_TESTENV_ENTITY_ID'),
        'sso_endpoint' => env('SPID_TESTENV_SSO'),
        'slo_endpoint' => env('SPID_TESTENV_SLO'),
        'x509cert' => env('SPID_TESTENV_CERT'),
    ] : false,

    'validator_idp' => env('SPID_VALIDATOR_IDP_ENABLED', false) ? [
        'entityId' => env('SPID_VALIDATOR_IDP_ENTITY_ID'),
        'sso_endpoint' => env('SPID_VALIDATOR_IDP_SSO'),
        'slo_endpoint' => env('SPID_VALIDATOR_IDP_SLO'),
        'x509cert' => env('SPID_VALIDATOR_IDP_CERT'),
    ] : false,

    'middleware_group' => 'web',
    'routes_prefix' => 'spid',
    'login_view' => 'auth.spid',
    'after_login_url' => '/analytics',
    'after_logout_url' => '/',
];
