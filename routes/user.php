<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::prefix('user')->middleware(['auth', 'is_user'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'userHome'])->name('user.dashboard');
});