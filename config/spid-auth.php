<?php
/**
 * This file contains the configuration options needed for SPIDAuth Package.
 *
 * @package Italia\SPIDAuth
 * @license BSD-3-clause
 */

return [
      'sp_entity_id' => 'https://spid.agid.gov.it/analytics',
      'sp_service_name' => 'Analytics Italia Service',
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
      'sp_certificate' => 'MIIEXjCCA0agAwIBAgIJALSvrCN1tPSpMA0GCSqGSIb3DQEBCwUAMHwxCzAJBgNVBAYTAklUMQ4wDAYDVQQIEwVMYXppbzENMAsGA1UEBxMEUm9tYTEmMCQGA1UEChMdQWdlbnppYSBwZXIgbCdJdGFsaWEgZGlnaXRhbGUxJjAkBgNVBAMTHWh0dHBzOi8vcGl3aWstb25ib2FyZGluZy10ZXN0MB4XDTE4MDMxNTA5NDIyN1oXDTE5MDMxNTA5NDIyN1owfDELMAkGA1UEBhMCSVQxDjAMBgNVBAgTBUxhemlvMQ0wCwYDVQQHEwRSb21hMSYwJAYDVQQKEx1BZ2VuemlhIHBlciBsJ0l0YWxpYSBkaWdpdGFsZTEmMCQGA1UEAxMdaHR0cHM6Ly9waXdpay1vbmJvYXJkaW5nLXRlc3QwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDN2LYAUleFqzYOHQU71Gw9Mg/3wGeGmFOS+b/Y6eYRaY2SWdFbbiPvztGHKj9ryxN/qqjAikpgjVgJP+av5VuAbrlEuV3IRiFmdxBImck44W0qk4GR+MmrmTOTj4D8xd/+HxKQLvFgoJC7Wly/kS2xSuoZmD39yU36A3KxSlL3F6/9Jdx51SHLzYR8H5hd/sjcW5whjHBZ09cOi/7o8nmAKl8eEOQ8fwfBwR+W2d0flOCfNAQQOQ9ZPUeZrL3cSdRL0DdcOnvT26mxmZTgi2J5X3dl/sdiSykD1mqpm6EbS0egQc7NFwXj4MLvj8YwBtHHqUjmy8sayLbSKNfMQ0BLAgMBAAGjgeIwgd8wHQYDVR0OBBYEFI8exqnE56JI59ytKCRrgXZLAMeaMIGvBgNVHSMEgacwgaSAFI8exqnE56JI59ytKCRrgXZLAMeaoYGApH4wfDELMAkGA1UEBhMCSVQxDjAMBgNVBAgTBUxhemlvMQ0wCwYDVQQHEwRSb21hMSYwJAYDVQQKEx1BZ2VuemlhIHBlciBsJ0l0YWxpYSBkaWdpdGFsZTEmMCQGA1UEAxMdaHR0cHM6Ly9waXdpay1vbmJvYXJkaW5nLXRlc3SCCQC0r6wjdbT0qTAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQAXf8z1SGm63GF/uAHq0c06RDuhsOj80VXd+YDXJabeJX57G5cZyYwvNpSa/Z2wLyU/Y5CMG005VFzuD59hfF+jqy8+1w2ETDFyvNijhNMiBtFAbbWv9sTh6/K8n7o8l8Kmb7IO9DD19LLX/9ouPPG9/kMW8E7+peBJv/6XUg1b85C3jxC5Z5jeuSs4PsSCvRtM8RtjLZPaMXWY66LakkZSmvkerUtwhHoE62v7Xk3FOnx3fnCTUm9WlVN5RthKXRp2eX1YsqvBMLan1xjJVV42XD64u2jvYBPei6cIdHpyWGN5CGadqJQqm7vQ9wLRcX0b/D0waUdOgB844LFCm6nS',
      'sp_private_key' => '{{SPID_SP_PRIVATE_KEY}}',

      'test_idp' => true,

      'middleware_group' => 'web',
      'routes_prefix' => 'spid',
      'login_view' => 'auth.spid',
      'after_login_url' => '/dashboard',
      'after_logout_url' => '/'
];
