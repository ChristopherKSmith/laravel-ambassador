<?php

use App\Http\Controllers\AmbassadorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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

//Admin
Route::prefix('admin')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware(['auth:sanctum', 'scope.admin'])->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::put('users/profile', [AuthController::class, 'updateProfile']);
        Route::put('users/password', [AuthController::class, 'updatePassword']);

        Route::get('users/{id}/links', [LinkController::class, 'index']);
        Route::get('orders', [OrderController::class, 'index']);

        Route::get('ambassadors', [AmbassadorController::class, 'index']);

        Route::apiResource('products', ProductController::class);

    });
});

//Ambassador

//Checkout
