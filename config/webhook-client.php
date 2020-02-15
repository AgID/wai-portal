<?php

return [
    'configs' => [
        [
            /*
             * This package support multiple webhook receiving endpoints. If you only have
             * one endpoint receiving webhooks, you can use 'default'.
             */
            'name' => 'closed-beta-whitelist',

            /*
             * We expect that every webhook call will be signed using a secret. This secret
             * is used to verify that the payload has not been tampered with.
             */
            'signing_secret' => env('CLOSED_BETA_WHITELIST_WEBHOOK_CLIENT_SECRET'),

            /*
             * The name of the header containing the signature.
             */
            'signature_header_name' => 'X-Hub-Signature',

            /*
             *  This class will verify that the content of the signature header is valid.
             *
             * It should implement \Spatie\WebhookClient\SignatureValidator\SignatureValidator
             */
            'signature_validator' => \App\Http\Middleware\GitHubSignatureValidator::class,

            /*
             * This class determines if the webhook call should be stored and processed.
             */
            'webhook_profile' => \App\Http\Middleware\ClosedBetaWhitelistUpdateWebhookProfile::class,

            /*
             * The classname of the model to be used to store call. The class should be equal
             * or extend Spatie\WebhookClient\Models\WebhookCall.
             */
            'webhook_model' => \App\Models\ClosedBetaWhitelist::class,

            /*
             * The class name of the job that will process the webhook request.
             *
             * This should be set to a class that extends \Spatie\WebhookClient\ProcessWebhookJob.
             */
            'process_webhook_job' => \App\Jobs\UpdateClosedBetaWhitelist::class,

            /*
             * Closed beta whitelist repository configuration
             */
            'repository' => [

                /*
                 * Repository full name (:owner/:repo)
                 */
                'full_name' => env('CLOSED_BETA_WHITELIST_REPOSITORY_FULL_NAME'),

                /*
                 * Repository branch
                 */
                'branch' => env('CLOSED_BETA_WHITELIST_REPOSITORY_BRANCH', 'master'),

                /*
                 * Repository branch file name
                 */
                'file_name' => env('CLOSED_BETA_WHITELIST_REPOSITORY_FILE_NAME', 'closed_beta_whitelist.yml'),
            ],
        ],
    ],
];
