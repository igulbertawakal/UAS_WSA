<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'application' => 'UAS WSA Mini E-Commerce API',
        'documentation' => url('/docs'),
        'api_login' => url('/api/login'),
    ]);
});
