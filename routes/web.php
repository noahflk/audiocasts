<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureSetupIsComplete;
use App\Http\Middleware\EnsureSetupIsIncomplete;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\AudiobookController;
use App\Http\Controllers\FileDownloadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware([EnsureSetupIsComplete::class, 'auth.web'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/audiobooks', [DashboardController::class, 'index']);
    Route::get('/audiobooks/{audiobook:slug}', [AudiobookController::class, 'show']);
    Route::get('/audiobooks/{audiobook:slug}/media/{file:id}', FileDownloadController::class);
});

Route::get('/feed', FeedController::class)->middleware('auth.feed');

// TODO: Setup must be incomplete
Route::get('/setup', function () {
    return view('setup');
})->middleware(EnsureSetupIsIncomplete::class);

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
