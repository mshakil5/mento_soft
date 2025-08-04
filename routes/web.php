<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;

require __DIR__.'/admin.php';
require __DIR__.'/manager.php';
require __DIR__.'/user.php';

Route::get('/clear', function () {
    Auth::logout();
    session()->flush();
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});

Route::fallback(fn () => redirect('/'));

Auth::routes();

Route::get('/', [FrontendController::class, 'index'])->name('homepage');
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
Route::post('/contact-us', [FrontendController::class, 'storeContact'])->name('contact.store');