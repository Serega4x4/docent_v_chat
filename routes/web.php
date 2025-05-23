<?php

use Illuminate\Support\Facades\Route;

Route::get('/up', function () {
    return response()->json(['status' => 'OK']);
});
