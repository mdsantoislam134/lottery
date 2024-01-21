<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//core apis
Route::post('/register', [AuthController::class, 'store']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

Route::post('/profile-update', [AuthController::class, 'profileupdate']);



Route::get('/orders', [OrderController::class, 'getorders']);

Route::get('/order-cancel/{id}', [OrderController::class, 'cancelorders']);


Route::post('/order-place', [OrderController::class, 'storeOrder']);

});
Route::post('/convert', [OrderController::class, 'convertHash']);