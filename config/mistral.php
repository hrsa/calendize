<?php

return [

    'api_key'       => env('MISTRAL_API_KEY'),
    'model'         => 'mistral-large-latest',
    'system_prompt' => env('OPENAI_SYSTEM_PROMPT')
        ? base64_decode(env('OPENAI_SYSTEM_PROMPT'))
        : '',
    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),
];
