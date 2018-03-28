<?php
/**
 * This file contains the configuration options needed for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

return [
      'sp_entity_id' => 'https://spid.agid.gov.it/webanalytics',
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

      'test_idp' => true,

      'middleware_group' => 'web',
      'routes_prefix' => 'spid',
      'login_view' => 'auth.spid',
      'after_login_url' => '/dashboard',
      'after_logout_url' => '/'
];
