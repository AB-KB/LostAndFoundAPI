<?php

use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\GeoController;
use App\Http\Controllers\API\MessageController as APIMessageController;
use App\Http\Controllers\API\StatisticsController;
use App\Http\Controllers\MessageController;
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


Route::prefix("v1")->group(function () {

    Route::prefix("auth")->group(function () {

        Route::post("login", [App\Http\Controllers\API\AuthController::class, "login"]);
        Route::post("register", [App\Http\Controllers\API\AuthController::class, "register"]);
    });

    Route::prefix("user")->middleware("auth:sanctum")->group(function () {

        Route::get("me", [App\Http\Controllers\API\UserController::class, "profile"]);
    });

    Route::prefix("geo")->group(function () {

        Route::get("provinces", [GeoController::class, "provinces"]);
        Route::get("provinces/{province_id}/districts", [GeoController::class, "districts"]);
        Route::get("districts/{district_id}/sectors", [GeoController::class, "sectors"]);
        Route::get("sectors/{sector_id}/cells", [GeoController::class, "cells"]);
        Route::get("cells/{cell_id}/villages", [GeoController::class, "villages"]);
    });



    Route::group(["middleware" => "auth:sanctum"], function () {

        Route::prefix("notifications")->group(function () {

            Route::put('', [App\Http\Controllers\API\NotificationAPIController::class, "index"]);
            Route::put('/{id}', [App\Http\Controllers\API\NotificationAPIController::class, "read"]);
        });

        Route::prefix("items")->group(function () {

            Route::get("", [App\Http\Controllers\API\ItemAPIController::class, "index"]);
            Route::post("", [App\Http\Controllers\API\ItemAPIController::class, "store"]);
            Route::get("{id}", [App\Http\Controllers\API\ItemAPIController::class, "show"])->whereNumber("id");
            Route::put("{id}", [App\Http\Controllers\API\ItemAPIController::class, "update"])->whereNumber("id");
            Route::delete("{id}", [App\Http\Controllers\API\ItemAPIController::class, "destroy"])->whereNumber("id");
            Route::get("{id}/claim", [App\Http\Controllers\API\ItemAPIController::class, "getClaim"])->whereNumber("id");
            Route::post("{id}/open-claim", [App\Http\Controllers\API\ItemAPIController::class, "openClaim"])->whereNumber("id");
        });

        Route::prefix("categories")->group(function () {

            Route::get("", [App\Http\Controllers\API\CategoryAPIController::class, "index"]);
            Route::post("", [App\Http\Controllers\API\CategoryAPIController::class, "store"])
                ->middleware("auth:admin");
            Route::put("{id}", [App\Http\Controllers\API\CategoryAPIController::class, "update"]);
            Route::delete("{id}", [App\Http\Controllers\API\CategoryAPIController::class, "destroy"]);
        });

        Route::group(["prefix" => "admin"], function () {

            Route::get("community", [DashboardController::class, "getComminityDetails"]);

            Route::get("popular-categories", [App\Http\Controllers\API\CategoryAPIController::class, "popular"]);

            Route::prefix("statistics")->group(function () {

                Route::get("active-members", [StatisticsController::class, "activeMembers"]);
                Route::get("lost-items", [StatisticsController::class, "lostItems"]);
                Route::get("found-items", [StatisticsController::class, "foundItems"]);
                Route::get("overall", [StatisticsController::class, "overall"]);
            });

            Route::get("list-users", [App\Http\Controllers\API\UserController::class, "list"]);

            Route::prefix("messages")->group(function () {

                Route::get("threads", [APIMessageController::class, "adminsThreads"]);
                Route::get("threads/{thread_id}", [APIMessageController::class, "threadMessage"])->whereNumber("thread_id");
                Route::post("reply/{thread_id}", [APIMessageController::class, "respondToMessage"])->whereNumber("message_id");
            });
        });
    });
});
