<?php
/**
 * This file contains the configuration options needed for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

return [
    'sp_entity_id' => 'https://webanalytics.italia.it',
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

    'test_idp' => 'production' === env('APP_ENV') ? false : [
        'entityId' => env('APP_URL') . ':8088',
        'sso_endpoint' => env('APP_URL') . ':8088/sso',
        'slo_endpoint' => env('APP_URL') . ':8088/slo',
        'x509cert' => 'MIICljCCAX4CCQDtboNbedrFLzANBgkqhkiG9w0BAQsFADANMQswCQYDVQQGEwJJVDAeFw0xOTAxMjMxMjA5MDVaFw0xOTAyMjIxMjA5MDVaMA0xCzAJBgNVBAYTAklUMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxt1xYjyuK1mi4j2bKlwAn0bQzAcyeYdwjYHqJ3RarPXntjQhjKMorwC5IbdN9JCT7T/ISeGbs1vFUbF5H22f/GLR9WRbHLiSlof1rw2gkYsimJw18nI4qqmk1cGfNIU/1Q73oXhZ135QjejZdO+0Ss/ToHzBlQz9U96EWdSVtTeohePHn1HKx5bk5FLfCtRAUzolsQH02IOmTAo5yvxhdKUoSCg8iv6c3St5F7eX9e0WgTFFUEyYzyZJYi1LzE/t3dHsRJ2RFf5opsvs03+9STwei9PRCEBZy9G34lYUbbarSLVXx/LX8+y0cZtRhoJKR+Mbx/bncsjyXYyXoJjCAQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQAWoj1MgTXhp6bQWNUgR2CR1XCZ9I1tpQkTnhpyVu0ndjzbRe+oDdOARSqyKOezrlrIWN4Ht7IisP/he04yGE295cm78fp7u2NaxrVizNJUw9hlHUs94o44NqlW+sQ6s7hDQfW6Pli0/sRWnCfLGethyXEHvMTlMm6w87UfHBHFdGr35OIy7Yin4rzw4DXOUrPLIyh5Ys9girZrdUMi+kY3pu7R5Hz2YjqhAObTxgm/JcU1oWiix3obxKo0jXU5HoXtXLmWoEB1QJb/Vfr7ai+UlIsRzD8HfTeJOANYojNfZlG46uXoXw1lTq8IARj8+tWT/Vq5hqimWtGh68vNNS2a',
    ],

    'validator_idp' => 'production' === env('APP_ENV') ? false : [
        'entityId' => 'http://spid-validator:8080',
        'sso_endpoint' => 'http://spid-validator:8080/samlsso',
        'slo_endpoint' => 'http://spid-validator:8080/samlsso',
        'x509cert' => 'MIIEGDCCAwCgAwIBAgIJAOrYj9oLEJCwMA0GCSqGSIb3DQEBCwUAMGUxCzAJBgNVBAYTAklUMQ4wDAYDVQQIEwVJdGFseTENMAsGA1UEBxMEUm9tZTENMAsGA1UEChMEQWdJRDESMBAGA1UECxMJQWdJRCBURVNUMRQwEgYDVQQDEwthZ2lkLmdvdi5pdDAeFw0xOTA0MTExMDAyMDhaFw0yNTAzMDgxMDAyMDhaMGUxCzAJBgNVBAYTAklUMQ4wDAYDVQQIEwVJdGFseTENMAsGA1UEBxMEUm9tZTENMAsGA1UEChMEQWdJRDESMBAGA1UECxMJQWdJRCBURVNUMRQwEgYDVQQDEwthZ2lkLmdvdi5pdDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAK8kJVo+ugRrbbv9xhXCuVrqi4B7/MQzQc62ocwlFFujJNd4m1mXkUHFbgvwhRkQqo2DAmFeHiwCkJT3K1eeXIFhNFFroEzGPzONyekLpjNvmYIs1CFvirGOj0bkEiGaKEs+/umzGjxIhy5JQlqXE96y1+Izp2QhJimDK0/KNij8I1bzxseP0Ygc4SFveKS+7QO+PrLzWklEWGMs4DM5Zc3VRK7g4LWPWZhKdImC1rnS+/lEmHSvHisdVp/DJtbSrZwSYTRvTTz5IZDSq4kAzrDfpj16h7b3t3nFGc8UoY2Ro4tRZ3ahJ2r3b79yK6C5phY7CAANuW3gDdhVjiBNYs0CAwEAAaOByjCBxzAdBgNVHQ4EFgQU3/7kV2tbdFtphbSA4LH7+w8SkcwwgZcGA1UdIwSBjzCBjIAU3/7kV2tbdFtphbSA4LH7+w8SkcyhaaRnMGUxCzAJBgNVBAYTAklUMQ4wDAYDVQQIEwVJdGFseTENMAsGA1UEBxMEUm9tZTENMAsGA1UEChMEQWdJRDESMBAGA1UECxMJQWdJRCBURVNUMRQwEgYDVQQDEwthZ2lkLmdvdi5pdIIJAOrYj9oLEJCwMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcNAQELBQADggEBAJNFqXg/V3aimJKUmUaqmQEEoSc3qvXFITvT5f5bKw9yk/NVhR6wndL+z/24h1OdRqs76blgH8k116qWNkkDtt0AlSjQOx5qvFYh1UviOjNdRI4WkYONSw+vuavcx+fB6O5JDHNmMhMySKTnmRqTkyhjrch7zaFIWUSV7hsBuxpqmrWDoLWdXbV3eFH3mINA5AoIY/m0bZtzZ7YNgiFWzxQgekpxd0vcTseMnCcXnsAlctdir0FoCZztxMuZjlBjwLTtM6Ry3/48LMM8Z+lw7NMciKLLTGQyU8XmKKSSOh0dGh5Lrlt5GxIIJkH81C0YimWebz8464QPL3RbLnTKg+c=',
    ],

    'middleware_group' => 'web',
    'routes_prefix' => 'spid',
    'login_view' => 'auth.spid',
    'after_login_url' => '/analytics',
    'after_logout_url' => '/',
];
