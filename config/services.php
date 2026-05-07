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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ML API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the Machine Learning service used for AI predictions.
    | Supports circuit breaker pattern, retry logic, and caching.
    |
    */

    'ml_api' => [
        'url' => env('ML_API_URL', 'http://localhost:8000'),
        'key' => env('ML_API_KEY', ''),
        'timeout' => env('ML_API_TIMEOUT', 30),
        'model_version' => env('ML_API_MODEL_VERSION', '1.0.0'),
        'cache_ttl' => env('ML_API_CACHE_TTL', 6), // hours

        // Circuit breaker configuration
        'circuit_breaker' => [
            'enabled' => env('ML_API_CIRCUIT_BREAKER_ENABLED', true),
            'failure_threshold' => env('ML_API_CIRCUIT_BREAKER_FAILURES', 5),
            'reset_timeout' => env('ML_API_CIRCUIT_BREAKER_RESET', 300), // seconds
        ],

        // Retry configuration
        'retry' => [
            'max_attempts' => env('ML_API_RETRY_MAX_ATTEMPTS', 3),
            'delay_ms' => env('ML_API_RETRY_DELAY_MS', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AI/ML services including feature extraction,
    | prediction engines, and monitoring.
    |
    */

    'ai' => [
        // Feature extraction settings
        'features' => [
            'cache_ttl' => env('AI_FEATURES_CACHE_TTL', 15), // minutes
            'batch_size' => env('AI_FEATURES_BATCH_SIZE', 100),
        ],

        // Prediction settings
        'prediction' => [
            'cache_ttl' => env('AI_PREDICTION_CACHE_TTL', 15), // minutes
            'confidence_threshold' => env('AI_PREDICTION_CONFIDENCE_THRESHOLD', 0.6),
            'fallback_enabled' => env('AI_PREDICTION_FALLBACK_ENABLED', true),
        ],

        // Monitoring settings
        'monitoring' => [
            'enabled' => env('AI_MONITORING_ENABLED', true),
            'log_channel' => env('AI_MONITORING_LOG_CHANNEL', 'ai'),
            'metrics_enabled' => env('AI_MONITORING_METRICS_ENABLED', true),
        ],

        // Model registry
        'models' => [
            'student_performance' => [
                'name' => 'Student Performance Predictor',
                'version' => '1.0.0',
                'type' => 'ensemble',
                'endpoint' => '/predict/performance',
            ],
            'capacity_prediction' => [
                'name' => 'Capacity Prediction',
                'version' => '1.0.0',
                'type' => 'regression',
                'endpoint' => '/predict/capacity',
            ],
            'early_warning' => [
                'name' => 'Early Warning System',
                'version' => '1.0.0',
                'type' => 'classification',
                'endpoint' => '/predict/risk',
            ],
        ],
    ],

];
