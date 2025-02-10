<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
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

//this is for products routes
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
//this is for categories routes
Route::middleware('auth:api')->group(function () {
      // فقط المدير (admin) يمكنه عرض قائمة الفئات
      Route::get('categories', [CategoryController::class, 'index']);

      // فقط المدير (admin) يمكنه إضافة فئة
      Route::post('categories', [CategoryController::class, 'store'])->middleware('role:admin');

      // فقط المدير (admin) يمكنه عرض معلومات فئة
      Route::get('categories/{id}', [CategoryController::class, 'show']);

      // فقط المدير (admin) يمكنه تعديل فئة
      Route::put('categories/{id}', [CategoryController::class, 'update'])->middleware('role:admin');

      // فقط المدير (admin) يمكنه حذف فئة
      Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->middleware('role:admin');
});

Route::middleware('auth:api')->group(function () {
      Route::post('carts', [CartController::class, 'addToCart'])->middleware('role:buyer');
      Route::put('carts/{id}', [CartController::class, 'updateCart'])->middleware('role:buyer');
      Route::delete('carts/{id}', [CartController::class, 'removeFromCart'])->middleware('role:buyer');
      Route::get('carts', [CartController::class, 'getCart'])->middleware('role:buyer');
      Route::delete('carts', [CartController::class, 'clearCart'])->middleware('role:buyer');
});
