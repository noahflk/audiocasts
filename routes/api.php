<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DirectoriesController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\ScanLibraryFiles;
use App\Http\Controllers\Api\FeedPropertiesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("/directories", [DirectoriesController::class, "list"]);
Route::post("/directories", [DirectoriesController::class, "create"]);
Route::delete("/directories/{media}", [DirectoriesController::class, "delete"]);
Route::post("/directories/check", [DirectoriesController::class, "check"]);

Route::get("/users", [UsersController::class, "get"]);
Route::post("/users", [UsersController::class, "create"]);
Route::patch("/users/{user:type}", [UsersController::class, "update"]);

Route::get("/settings", [SettingsController::class, "get"]);
Route::get("/settings/{key}", [SettingsController::class, "getOne"]);
Route::post("/settings", [SettingsController::class, "update"]);
Route::delete("/settings/{setting:key}", [SettingsController::class, "delete"]);

Route::post("/feed", [FeedPropertiesController::class, "storeSettings"]);
Route::post("/feed/cover", [FeedPropertiesController::class, "uploadCover"]);

Route::post("/scan", ScanLibraryFiles::class);
