<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Higgsfield AI video generation. Powers the cinematic promo-video feature
    // (image-to-video) per car. Credentials use the KEY_ID:KEY_SECRET format and
    // are sent as an "Authorization: Key <credentials>" header.
    'higgsfield' => [
        'credentials' => env('HIGGSFIELD_CREDENTIALS'),
        'base_url' => env('HIGGSFIELD_BASE_URL', 'https://platform.higgsfield.ai'),
        'model' => env('HIGGSFIELD_MODEL', 'dop-turbo'),
        // Authorization scheme: "Key" (KEY_ID:KEY_SECRET) or "Bearer" (single token).
        'auth_scheme' => env('HIGGSFIELD_AUTH_SCHEME', 'Key'),
        // Send Higgsfield's own prompt-enhancement flag. Disable if the account rejects it.
        'enhance_prompt' => env('HIGGSFIELD_ENHANCE_PROMPT', true),
    ],

    // VWE / Finnik data services. Powers the premium parts of het voertuigrapport
    // (taxatie, tellerstandhistorie, onderhoudshistorie, terugroepdetails, schade,
    // WOK, fabrieksopties). Leave empty to keep the report on free RDW data only.
    'vwe' => [
        'base_url' => env('VWE_API_BASE_URL'),
        'username' => env('VWE_USERNAME'),
        'password' => env('VWE_PASSWORD'),
        'client_id' => env('VWE_CLIENT_ID'),
    ],

];
