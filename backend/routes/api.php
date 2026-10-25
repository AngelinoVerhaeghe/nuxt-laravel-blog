<?php

use App\Http\Controllers\Api\v1\PostController;
use App\Http\Controllers\Api\v1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
	Route::get('/posts', [PostController::class, 'index']);
	Route::get('/posts/{post:slug}', [PostController::class, 'show']);

	Route::get('/products', [ProductController::class, 'index']);
	Route::get('/products/{product:slug}', [ProductController::class, 'show']);
});
