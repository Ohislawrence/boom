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

    'football_api' => [
        'key'           => env('FOOTBALL_API_KEY'),
        'base_url'      => env('FOOTBALL_API_BASE', 'https://v3.football.api-sports.io'),
        'h2h_limit'     => (int) env('FOOTBALL_H2H_LIMIT', 10),
        'form_matches'  => (int) env('FOOTBALL_FORM_MATCHES', 5),
    ],

    'deepseek' => [
        'key'                  => env('DEEPSEEK_API_KEY'),
        'url'                  => env('DEEPSEEK_API_URL', 'https://api.deepseek.com/v1/chat/completions'),
        'model'                => env('DEEPSEEK_MODEL', 'deepseek-chat'),
        'confidence_threshold' => (int) env('DEEPSEEK_CONFIDENCE_THRESHOLD', 75),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
