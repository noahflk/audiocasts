<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureSetupIsComplete;
use App\Http\Middleware\EnsureSetupIsIncomplete;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;

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

Route::middleware([EnsureSetupIsComplete::class, "auth.web"])->group(function () {
    Route::get('/settings/1', function () {
        return view('settings-1');
    });

    Route::get('/settings/2', function () {
        return view('settings-2');
    });

    Route::get("/", [DashboardController::class, "index"]);
    Route::get("/dashboard", [DashboardController::class, "index"]);
});

Route::get("/feed", FeedController::class)->middleware("auth.basic");

// TODO: Setup must be incomplete
Route::get('/setup', function () {
    return view('setup');
})->middleware(EnsureSetupIsIncomplete::class);

Route::get('/login', [AuthController::class, "show"])->name("login");
Route::post('/login', [AuthController::class, "login"]);
Route::post('/logout', [AuthController::class, "logout"])->name("logout");
