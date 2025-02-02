<?php

return [

    'api_key'       => env('OPENAI_API_KEY'),
    'organization'  => env('OPENAI_ORGANIZATION'),
    'model'         => 'o3-mini',
    'system_prompt' => env('OPENAI_SYSTEM_PROMPT')
        ? base64_decode(env('OPENAI_SYSTEM_PROMPT'))
        : '',

    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),
];
