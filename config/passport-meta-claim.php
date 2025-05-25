<?php

/**
 * Configuration settings for claim classes.
 *
 * @return array<string, mixed> Configuration parameters
 */
return [
    /**
     * The default path where claim classes are stored.
     *
     * It can be overridden using the `CLAIMS_PATH` environment variable.
     *
     * @var string
     */
    'path' => env('CLAIMS_PATH', app_path('Claims')),

    /**
     * The suffix required for valid claim class names.
     *
     * Only classes that end with this suffix will be processed.
     *
     * @var string
     */
    'suffix' => 'Claim',
];
