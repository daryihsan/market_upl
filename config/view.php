<?php

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => env('VIEW_COMPILED_PATH', realpath(storage_path('framework/views'))),

    'cache' => env('VIEW_CACHE', true),

    'compiled_extension' => env('VIEW_COMPILED_EXTENSION', 'php'),

    'check_cache_timestamps' => env('VIEW_CHECK_CACHE_TIMESTAMPS', true),

    'relative_hash' => env('VIEW_RELATIVE_HASH', false),
];
