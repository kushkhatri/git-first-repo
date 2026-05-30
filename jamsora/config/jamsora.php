<?php

return [
    'igi' => [
        'fee' => (float) env('JAMSORA_IGI_FEE', 100),
        'extra_delivery_days' => (int) env('JAMSORA_IGI_EXTRA_DAYS', 7),
    ],

    'import' => [
        // Max CSV upload size in kilobytes (100 MB default).
        'max_upload_kb' => (int) env('JAMSORA_IMPORT_MAX_KB', 102400),
        'allowed_mimes' => ['csv', 'txt'],
    ],

    'reserved_slugs' => [
        'shop', 'cart', 'checkout', 'product', 'page', 'admin', 'login', 'register',
        'password', 'dashboard', 'profile', 'api', 'storage', 'build', 'up',
    ],
];
