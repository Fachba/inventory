<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoodReceiveController;
use App\Http\Controllers\LogStatusController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RequestProductController;
use App\Http\Controllers\StockOpnameController;
use App\Models\StockOpname;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', function () {
        return response()->json(['message' => 'Authorized']);
    });
});

Route::middleware(['jwta.auth'])->group(function () {

    Route::post('/status', [LogStatusController::class, 'create'])->middleware('roleAction:json');


    Route::get('/status', [LogStatusController::class, 'index']);
    Route::apiResource('products', ProductController::class);

    Route::prefix('purchase-orders')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index']);
        Route::get('/{id}', [PurchaseOrderController::class, 'show']);
        Route::post('/', [PurchaseOrderController::class, 'store']);
        Route::put('/{id}', [PurchaseOrderController::class, 'update']);
        Route::delete('/{id}', [PurchaseOrderController::class, 'destroy']);
    });

    Route::prefix('request-products')->group(function () {
        Route::get('/', [RequestProductController::class, 'index']);
        Route::get('/{id}', [RequestProductController::class, 'show']);
        Route::post('/', [RequestProductController::class, 'store']);
        Route::put('/{id}', [RequestProductController::class, 'update']);
        Route::delete('/{id}', [RequestProductController::class, 'destroy']);
    });

    Route::prefix('good-receives')->group(function () {
        Route::get('/', [GoodReceiveController::class, 'index']);
        Route::get('/{id}', [GoodReceiveController::class, 'show']);
        Route::post('/', [GoodReceiveController::class, 'store']);
        Route::put('/{id}', [GoodReceiveController::class, 'update']);
        Route::delete('/{id}', [GoodReceiveController::class, 'destroy']);
    });

    Route::prefix('stock-opnames')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index']);
        Route::get('/{id}', [StockOpnameController::class, 'show']);
        Route::post('/', [StockOpnameController::class, 'store']);
        Route::put('/input/{id}', [StockOpnameController::class, 'inputOpname']);
        Route::put('/{id}', [StockOpnameController::class, 'update']);
        Route::delete('/{id}', [StockOpnameController::class, 'destroy']);
    });
});
