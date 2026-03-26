<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->controller(PostController::class)->group(function () {
    Route::get('/posts', 'index');
    Route::post('/posts', 'store');
    Route::get('/posts/{post}', 'show');
    Route::put('/posts/{post}', 'update');
    Route::delete('/posts/{post}', 'destroy');
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});