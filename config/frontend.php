<?php

return [
    'url' => env('FRONT_URL', 'http://localhost:3000'),
    'transaction-fail' => env('FRONT_URL', 'http://localhost:3000') . '/transaction/failed',
    'transaction-success' => env('FRONT_URL', 'http://localhost:3000') . '/transaction/success',
];
