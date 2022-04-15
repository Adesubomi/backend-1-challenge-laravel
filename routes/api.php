<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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

Route::post('users', [UserController::class, 'store'])->name('users.store');

Route::middleware(['auth:sanctum'])->group( function () {
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('users', [UserController::class, 'update'])->name('users.update');
    Route::delete('users', [UserController::class, 'delete'])->name('users.delete');
    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::get('users/products', [ProductController::class, 'myProducts'])->name('users.products');

    Route::resource('products', ProductController::class)
        ->only(['index', 'show', 'store', 'update', 'delete']);
});
