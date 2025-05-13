<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key and Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API Key and organization. This will be
    | used to authenticate with the OpenAI API - you can find your API key
    | and organization on your OpenAI dashboard, at https://openai.com.
    */

    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Proxy Settings
    |--------------------------------------------------------------------------
    |
    | Here you may specify proxy settings for the OpenAI API client.
    | This is useful when you need to use a proxy to access the OpenAI API.
    */

    'proxy_url' => env('OPENAI_PROXY', 'http://proxy.example.com:8080'),
    'verify_ssl' => env('OPENAI_VERIFY_SSL', true),
];
