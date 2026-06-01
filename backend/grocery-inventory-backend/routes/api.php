<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\UnitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Primary API routes (canonical, unversioned for backward compatibility)
|--------------------------------------------------------------------------
*/

Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:login')
    ->name('auth.login');
Route::get('/status', [StatusController::class, 'show'])->name('status.show');

Route::middleware('auth:api')->group(function (): void {
    Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('subcategories', SubcategoryController::class);
    Route::apiResource('units', UnitController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('items', ItemController::class);

    Route::get('/items/{item}/movements', [ItemController::class, 'movements'])->name('items.movements');

    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    Route::get('/lookups/categories', [LookupController::class, 'categories'])->name('lookups.categories');
    Route::get('/lookups/subcategories', [LookupController::class, 'subcategories'])->name('lookups.subcategories');
    Route::get('/lookups/units', [LookupController::class, 'units'])->name('lookups.units');
    Route::get('/lookups/suppliers', [LookupController::class, 'suppliers'])->name('lookups.suppliers');
});
