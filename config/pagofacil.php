<?php

return [
    'api_url' => env('PAGOFACIL_API_URL', 'https://api.pagofacil.com.bo/api'),
    'token_service' => env('PAGOFACIL_TOKEN_SERVICE'),
    'token_secret' => env('PAGOFACIL_TOKEN_SECRET'),
    'callback_url' => env('PAGOFACIL_CALLBACK_URL'),

    // Métodos de pago disponibles
    'payment_methods' => [
        'qr' => 1,
        'tarjeta' => 2,
        'tigo_money' => 3,
    ],

    // Tipos de documento
    'document_types' => [
        'ci' => 1,
        'nit' => 2,
        'pasaporte' => 3,
    ],

    // Monedas
    'currencies' => [
        'bolivianos' => 2,
        'dolares' => 1,
    ],
];