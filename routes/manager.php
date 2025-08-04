<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::prefix('manager')->middleware(['auth', 'is_manager'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'managerHome'])->name('manager.dashboard');
});