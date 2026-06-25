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
        // DoP first-last-frame variant (turbo/lite/standard) for the walk-around clips.
        'flf_variant' => env('HIGGSFIELD_FLF_VARIANT', 'turbo'),
    ],

    // fal.ai video generation. The official Seedance 2.0 API: one call with up to
    // nine car photos returns a single cinematic montage with native audio. The key
    // is a single token sent as "Authorization: Key <key>".
    'fal' => [
        'key' => env('FAL_KEY'),
        'base_url' => env('FAL_BASE_URL', 'https://queue.fal.run'),
        'model' => env('FAL_MODEL', 'bytedance/seedance-2.0/reference-to-video'),
        'resolution' => env('FAL_RESOLUTION', '720p'),
        'duration' => env('FAL_DURATION', 'auto'),
        'aspect_ratio' => env('FAL_ASPECT_RATIO', '16:9'),
        // Native audio. fal's moderation can flag generated music as sensitive;
        // set FAL_GENERATE_AUDIO=false for a guaranteed silent render.
        'generate_audio' => env('FAL_GENERATE_AUDIO', true),
    ],

    // Path to the ffmpeg binary, used to stitch per-photo clips into one reel.
    // Local dev points at the bundled Laragon binary; on the server set FFMPEG_PATH
    // to "ffmpeg" (if on PATH) or the absolute path.
    'ffmpeg' => [
        'path' => env('FFMPEG_PATH', 'ffmpeg'),
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
