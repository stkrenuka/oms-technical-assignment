<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;


Route::group(['middleware' => 'auth:sanctum'], static function () {
     Route::get('/user', [ProfileController::class, 'user']);

});
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // admin-only routes
});


Route::middleware('auth:sanctum')->group(function () {
});
