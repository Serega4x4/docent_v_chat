<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/up', function (Request $request) {
    \Illuminate\Support\Facades\Log::info('Root route accessed');
    return response()->json(['status' => 'ok']);
});

Route::get('/api', function (Request $request) {
    \Illuminate\Support\Facades\Log::info('API route accessed');
    return response()->json(['status' => 'API is running']);
});