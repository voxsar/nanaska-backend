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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'n8n' => [
        'upload_url' => env('N8N_UPLOAD_URL'),
		'upload_test_url' => env('N8N_UPLOAD_TEST_URL'),
        'marking_url' => env('N8N_MARKING_URL'),
		'marking_test_url' => env('N8N_MARKING_TEST_URL'),
        'question_url' => env('N8N_QUESTION_URL'),
		'question_test_url' => env('N8N_QUESTION_TEST_URL'),
		'ask_preseen_url' => env('N8N_ASK_PRESEEN_URL'),
		'ask_preseen_test_url' => env('N8N_ASK_PRESEEN_TEST_URL'),
		'analysis_model_url' => env('N8N_ANALYSIS_MODEL_URL'),
		'analysis_test_model_url' => env('N8N_ANALYSIS_TEST_MODEL_URL'),
    ],

];
