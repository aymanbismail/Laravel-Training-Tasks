<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Auth-protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Products (CRUD)
    Route::resource('products', ProductController::class);

    // Product Trash Management
    Route::get('products-trash', [ProductController::class, 'trash'])->name('products.trash');
    Route::post('products-trash/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('products-trash/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.forceDelete');
    Route::post('products-trash/bulk-restore', [ProductController::class, 'bulkRestore'])->name('products.bulkRestore');
    Route::delete('products-trash/bulk-force-delete', [ProductController::class, 'bulkForceDelete'])->name('products.bulkForceDelete');

    // Categories (read-only for now)
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');

    // Suppliers (read-only for now)
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
});

require __DIR__ . '/auth.php';
