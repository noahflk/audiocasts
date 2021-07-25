<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureSetupIsComplete;
use App\Http\Middleware\EnsureSetupIsIncomplete;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\AudiobookController;
use App\Http\Controllers\FileDownloadController;

Route::middleware([EnsureSetupIsComplete::class, 'auth.web'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/audiobooks', [DashboardController::class, 'index']);
    Route::get('/audiobooks/{audiobook:slug}', [AudiobookController::class, 'show']);
});

Route::middleware([EnsureSetupIsComplete::class, 'auth.feed'])->group(function () {
    Route::get('/feed', FeedController::class);
    Route::get('/audiobooks/{audiobook:slug}/media/{file:id}', FileDownloadController::class);
});


Route::get('/setup', function () {
    return view('setup');
})->middleware(EnsureSetupIsIncomplete::class);

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
