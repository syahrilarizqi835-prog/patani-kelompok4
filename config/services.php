<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Konfigurasi API key untuk layanan pihak ketiga yang digunakan aplikasi.
    |
    */

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model'   => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
    ],

    'openweathermap' => [
        'api_key' => env('OPENWEATHERMAP_API_KEY', ''),
        'city'    => env('OPENWEATHERMAP_CITY', 'Indramayu'),
    ],

];
