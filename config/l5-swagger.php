<?php

return [
    'api' => [
        /*
        |--------------------------------------------------------------------------
        | Swagger UI Settings
        |--------------------------------------------------------------------------
        */

        'title' => 'Movie Auditions API UI',
        'version' => '1.0.0',
        
        /*
        |--------------------------------------------------------------------------
        | Swagger UI Route Settings
        |--------------------------------------------------------------------------
        */

        'routes' => [
            /*
            |--------------------------------------------------------------------------
            | Route for accessing api documentation interface
            |--------------------------------------------------------------------------
            */

            'api' => 'api/documentation',

            /*
            |--------------------------------------------------------------------------
            | Route for accessing parsed swagger annotations.
            |--------------------------------------------------------------------------
            */

            'docs' => 'docs',

            /*
            |--------------------------------------------------------------------------
            | Route for Oauth2 authentication callback.
            |--------------------------------------------------------------------------
            */

            'oauth2_callback' => 'api/oauth2-callback',

            /*
            |--------------------------------------------------------------------------
            | Route for serving assets (images, fonts, etc.)
            |--------------------------------------------------------------------------
            */

            'assets' => 'docs.asset',

            /*
            |--------------------------------------------------------------------------
            | Middleware allows you to customize the middleware
            |--------------------------------------------------------------------------
            */

            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Paths to scan for annotations
        |--------------------------------------------------------------------------
        */

        'paths' => [
            /*
             * Absolute path to directory containing the swagger annotations are stored.
            */
            'annotations' => [
                base_path('app/Http/Controllers/API'),
            ],

            /*
             * Absolute path to directories that you would like to exclude from swagger generation
             */
            'excludes' => [],

            /*
             * Absolute path to directory where to export views
             */
            'views' => base_path('resources/views/vendor/l5-swagger'),

            /*
             * Edit to set the api's base path
            */
            'base' => env('L5_SWAGGER_BASE_PATH', null),

            /*
             * Absolute path to directories that you would like to exclude from swagger generation
             */
            'swagger_version' => env('SWAGGER_VERSION', '3.0'),
        ],

        /*
        |--------------------------------------------------------------------------
        | API Security Settings
        |--------------------------------------------------------------------------
        */

        'security' => [
            /*
             * Examples of Security
            */
            [
                /*
                'oauth2_security_example' => [
                    'read',
                    'write'
                ],

                'passport' => []
                */
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Constants which can be used in annotations
        |--------------------------------------------------------------------------
        */

        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('APP_URL').'/api',
        ],
    ],
];