<?php

return [

    // ...your other services...

    'zerogpt' => [
        'key'    => env('ZERO_GPT_API_KEY'),
        // "business" (api.zerogpt.com) or "rapidapi" (zerogpt.p.rapidapi.com)
        'flavor' => env('ZERO_GPT_API_FLAVOR', 'business'),
        'base'   => env('ZERO_GPT_API_BASE', 'https://api.zerogpt.com'),
    ],

];
