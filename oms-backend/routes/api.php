<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\ProductController as CustomerProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
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
        Route::post('/customers', [UserController::class, 'addCustomer']);

        Route::put('/customers/{customer}', [UserController::class, 'updateCustomer']);
        Route::get('/customers/search', [UserController::class, 'search']);
        Route::delete('/customers/{user}', [UserController::class, 'destroy']);
    });
// Customer – Products (public)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('products', [CustomerProductController::class, 'index']);
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/statuses', [OrderController::class, 'statuses']);
    Route::post('orders/{order}/change-status', [OrderController::class, 'changeStatus']);
    Route::get('orders/{order}/status-history', [OrderController::class, 'statusHistory']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']); // ✅ THIS
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::delete('orders/{order}', [OrderController::class, 'destroy']);
    Route::get('orders/{order}/invoice', [OrderController::class, 'downloadInvoice']);
    Route::post('update/order', [OrderController::class, 'updateOrder']);
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
});
