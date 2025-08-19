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

Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
Route::get('/', [FrontendController::class, 'index'])->name('homepage');
Route::get('/contact-us', [FrontendController::class, 'contact'])->name('contact');
Route::post('/contact-us', [FrontendController::class, 'storeContact'])->name('contact.store');
Route::get('/get-quotation', [FrontendController::class, 'getQuotation'])->name('quotation');
Route::post('/quotation/store', [FrontendController::class, 'storeQuotation'])->name('quotation.store');
Route::get('/portfolio', [FrontendController::class, 'portfolio'])->name('portfolio');
Route::get('/portfolio/{slug}', [FrontendController::class, 'portfolioDetails'])->name('portfolioDetails');
Route::get('/product/{slug}', [FrontendController::class, 'productDetails'])->name('productDetails');
Route::get('/privacy-policy', [FrontendController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-and-conditions', [FrontendController::class, 'termsAndConditions'])->name('terms-and-conditions');
Route::get('/frequently-asked-questions', [FrontendController::class, 'frequentlyAskedQuestions'])->name('frequently-asked-questions');