<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->post('refresh-token', [AuthController::class, 'refreshToken']);
Route::middleware('auth:api')->get('profile', [AuthController::class, 'profile']);
Route::middleware('auth:api')->post('request-seller-upgrade', [AuthController::class, 'requestSellerUpgrade']);

Route::group(['middleware' => 'auth:api'], function () {
      // أي مستخدم مصادق عليه يمكنه رؤية قائمة المنتجات
      Route::get('products', [ProductController::class, 'index']);

      // فقط المدير (admin) أو البائع (seller) يمكنهم إضافة منتج
      Route::post('products', [ProductController::class, 'store'])->middleware('role:seller|admin');

      // فقط المدير (admin) أو البائع (seller) يمكنهم تعديل منتج
      Route::put('products/{id}', [ProductController::class, 'update'])->middleware('role:seller|admin');

      // فقط المدير (admin) أو البائع (seller) يمكنهم حذف منتج
      Route::delete('products/{id}', [ProductController::class, 'destroy'])->middleware('role:seller|admin');

      // فقط المشتري (buyer) يمكنه إضافة/إزالة منتج من المفضلة
      Route::post('products/{id}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->middleware('role:buyer');
});