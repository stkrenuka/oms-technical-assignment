<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\ProductController as CustomerProductController;



// Route::post('/login', [AuthenticatedSessionController::class, 'login']);
Route::post('/login', [AuthenticatedSessionController::class, 'login']);
Route::post('/register', [AuthenticatedSessionController::class, 'register']);


Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])
    ->middleware('auth:sanctum');



Route::delete('/admin/customers/{user}', [UserController::class, 'destroy'])
    ->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Admin – Products
Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::resource('products', AdminProductController::class);
        Route::patch('products/{product}/status', [AdminProductController::class, 'changeStatus']);
        Route::get('/customers', [UserController::class, 'customers']);
        Route::delete('/customers/{user}', [UserController::class, 'destroy']);
    });

// Customer – Products (public)
Route::get('/products', [CustomerProductController::class, 'index']);