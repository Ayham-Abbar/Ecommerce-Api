<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);// logout


Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->post('refresh-token', [AuthController::class, 'refreshToken']);
Route::middleware('auth:api')->get('profile', [AuthController::class, 'profile']);
Route::middleware('auth:api')->post('request-seller-upgrade', [AuthController::class, 'requestSellerUpgrade']);