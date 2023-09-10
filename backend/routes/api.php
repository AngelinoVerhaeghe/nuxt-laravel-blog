<?php

use App\Http\Controllers\Api\v1\PostController;
use App\Http\Controllers\Api\v1\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
	Route::get('/posts', [PostController::class, 'index']);
	Route::get('/posts/{post:slug}', [PostController::class, 'show']);

	Route::get('/products', [ProductController::class, 'index']);
	Route::get('/products/{product:slug}', [ProductController::class, 'show']);
});
