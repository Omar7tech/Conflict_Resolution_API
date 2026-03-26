<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->controller(PostController::class)->group(function () {
    Route::get('/posts', 'index');
    Route::get('/posts/{id}', 'show');
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});