<?php

return [
    // Model weights and endpoints
    'huggingface' => [
        'token' => env('HUGGINGFACE_TOKEN', ''),
        'models' => [
            // weights sum doesn't need to be 1; they'll be normalized
            'roberta_openai_detector' => [
                'id' => 'roberta-base-openai-detector',
                'weight' => 0.35,
            ],
            'dialo_gpt_medium_ai_detector' => [
                'id' => 'microsoft/DialoGPT-medium-ai-detector',
                'weight' => 0.25,
            ],
            'chatgpt_detector_roberta' => [
                'id' => 'Hello-SimpleAI/chatgpt-detector-roberta',
                'weight' => 0.20,
            ],
            'gpt2_detector' => [
                'id' => 'gpt2-detector',
                'weight' => 0.20,
            ],
        ],
    ],

    'http' => [
        'timeout' => 15, // seconds
        'retries' => 2,
        'retry_base_delay_ms' => 400,
    ],

    'cache' => [
        'ttl_seconds' => env('CONTENT_DETECTION_CACHE_TTL', 86400), // 24h
    ],

    'thresholds' => [
        'ai' => 0.70,
        'human' => 0.30,
    ],

    'limits' => [
        'max_chars' => 20000,
    ],

    'rate_limit' => [
        'per_hour' => 100,
    ],
];
