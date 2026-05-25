<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperadminAccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperadminDashboardController;
use App\Http\Controllers\SuperadminMenuCategoryController;
use App\Http\Controllers\SuperadminMenuController;
use App\Http\Controllers\SuperadminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->role === 'superadmin' ? 'superadmin.dashboard' : 'dashboard')
        : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth', 'superadmin'])
    ->group(function () {
        Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', SuperadminUserController::class)->except(['show']);
        Route::get('/access', [SuperadminAccessController::class, 'index'])->name('access.index');
        Route::get('/access/{user}/edit', [SuperadminAccessController::class, 'edit'])->name('access.edit');
        Route::put('/access/{user}', [SuperadminAccessController::class, 'update'])->name('access.update');
        Route::resource('menus', SuperadminMenuController::class)->except(['show']);
        Route::resource('menu-categories', SuperadminMenuCategoryController::class)
            ->parameters(['menu-categories' => 'menuCategory'])
            ->except(['show']);
});
