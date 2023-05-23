<?php

use App\Http\Controllers\API\GeoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("v1")->group(function () {

    Route::prefix("auth")->group(function () {

        Route::post("login", [App\Http\Controllers\API\AuthController::class, "login"]);
        Route::post("register", [App\Http\Controllers\API\AuthController::class, "register"]);
    });


    Route::group(["middleware" => "auth"], function () {

        Route::resource('categories', App\Http\Controllers\API\CategoryAPIController::class)
            ->except(['create', 'edit']);


        Route::put('notifications', [App\Http\Controllers\API\NotificationAPIController::class, "index"]);
        Route::put('notifications/{id}', [App\Http\Controllers\API\NotificationAPIController::class, "read"]);


        Route::prefix("geo")->group(function () {

            Route::get("provinces", [GeoController::class, "provinces"]);
            Route::get("provinces/{province_id}/districts", [GeoController::class, "districts"]);
            Route::get("districts/{district_id}/sectors", [GeoController::class, "sectors"]);
            Route::get("sectors/{sector_id}/cells", [GeoController::class, "cells"]);
            Route::get("cells/{cell_id}/villages", [GeoController::class, "villages"]);
        });
    });
});
