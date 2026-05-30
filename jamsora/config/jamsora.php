<?php

return [
    'igi' => [
        'fee' => (float) env('JAMSORA_IGI_FEE', 100),
        'extra_delivery_days' => (int) env('JAMSORA_IGI_EXTRA_DAYS', 7),
    ],

    'reserved_slugs' => [
        'shop', 'cart', 'checkout', 'product', 'page', 'admin', 'login', 'register',
        'password', 'dashboard', 'profile', 'api', 'storage', 'build', 'up',
    ],
];
