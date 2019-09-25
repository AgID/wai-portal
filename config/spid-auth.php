<?php
/**
 * This file contains the configuration options needed for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

return [
      'sp_entity_id' => 'https://spid.agid.gov.it',
      'sp_base_url' => env('APP_URL'),
      'sp_service_name' => 'Web Analytics Italia Service',
      'sp_organization_name' => 'AGID',
      'sp_organization_display_name' => "Agenzia per l'Italia digitale",
      'sp_organization_url' => 'https://www.agid.gov.it',
      'sp_requested_attributes' => [
          'spidCode',
          'name',
          'familyName',
          #'placeOfBirth',
          #'countyOfBirth',
          #'dateOfBirth',
          #'gender',
          'fiscalNumber',
          #'companyName',
          #'registeredOffice',
          #'ivaCode',
          #'idCard',
          #'mobilePhone',
          #'email',
          #'address',
          #'digitalAddress',
          #'expirationDate'
      ],
      'sp_certificate' => env('SPID_SP_CERTIFICATE'),
      'sp_private_key' => env('SPID_SP_PRIVATE_KEY'),

      'test_idp' => 'production' === env('APP_ENV') ? false : [
        'entityId' => env('APP_URL') . ':8088',
        'sso_endpoint' => env('APP_URL') . ':8088/sso',
        'slo_endpoint' => env('APP_URL') . ':8088/slo',
        'x509cert' => 'MIICljCCAX4CCQDtboNbedrFLzANBgkqhkiG9w0BAQsFADANMQswCQYDVQQGEwJJVDAeFw0xOTAxMjMxMjA5MDVaFw0xOTAyMjIxMjA5MDVaMA0xCzAJBgNVBAYTAklUMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxt1xYjyuK1mi4j2bKlwAn0bQzAcyeYdwjYHqJ3RarPXntjQhjKMorwC5IbdN9JCT7T/ISeGbs1vFUbF5H22f/GLR9WRbHLiSlof1rw2gkYsimJw18nI4qqmk1cGfNIU/1Q73oXhZ135QjejZdO+0Ss/ToHzBlQz9U96EWdSVtTeohePHn1HKx5bk5FLfCtRAUzolsQH02IOmTAo5yvxhdKUoSCg8iv6c3St5F7eX9e0WgTFFUEyYzyZJYi1LzE/t3dHsRJ2RFf5opsvs03+9STwei9PRCEBZy9G34lYUbbarSLVXx/LX8+y0cZtRhoJKR+Mbx/bncsjyXYyXoJjCAQIDAQABMA0GCSqGSIb3DQEBCwUAA4IBAQAWoj1MgTXhp6bQWNUgR2CR1XCZ9I1tpQkTnhpyVu0ndjzbRe+oDdOARSqyKOezrlrIWN4Ht7IisP/he04yGE295cm78fp7u2NaxrVizNJUw9hlHUs94o44NqlW+sQ6s7hDQfW6Pli0/sRWnCfLGethyXEHvMTlMm6w87UfHBHFdGr35OIy7Yin4rzw4DXOUrPLIyh5Ys9girZrdUMi+kY3pu7R5Hz2YjqhAObTxgm/JcU1oWiix3obxKo0jXU5HoXtXLmWoEB1QJb/Vfr7ai+UlIsRzD8HfTeJOANYojNfZlG46uXoXw1lTq8IARj8+tWT/Vq5hqimWtGh68vNNS2a'
      ],

      'middleware_group' => 'web',
      'routes_prefix' => 'spid',
      'login_view' => 'auth.spid',
      'after_login_url' => '/dashboard',
      'after_logout_url' => '/'
];
