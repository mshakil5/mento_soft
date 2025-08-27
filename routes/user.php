<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

Route::prefix('user')->middleware(['auth', 'is_user'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'userHome'])->name('user.dashboard');

    Route::get('/projects', [UserController::class, 'projects'])->name('user.projects');
    Route::get('/tasks', [UserController::class, 'tasks'])->name('user.tasks');

    Route::get('/profile', [UserController::class, 'userProfile'])->name('user.profile');
    Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('user.profile.update');

    Route::get('/password', [UserController::class, 'userPassword'])->name('user.password');
    Route::post('/update-password', [UserController::class, 'updatePassword'])->name('user.password.update');
});