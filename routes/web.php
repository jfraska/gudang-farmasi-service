<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return 'GUDANG-SERVICE';
});

Route::resource('/users', UserController::class);

Route::group(['middleware' => 'after'], function () use ($router) {
    Route::group(['prefix' => 'purchase-order'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\PurchaseOrderController::class, 'index'])->middleware('apimid:purchaseOrder:all');
        Route::post('/', [App\Http\Controllers\PurchaseOrderController::class, 'store'])->middleware('apimid:purchaseOrder:store');
        Route::delete('/', [App\Http\Controllers\PurchaseOrderController::class, 'destroy'])->middleware('apimid:purchaseOrder:destroy');
        Route::get('/show', [App\Http\Controllers\PurchaseOrderController::class, 'show'])->middleware('apimid:purchaseOrder:show');
    });

    Route::group(['prefix' => 'receive'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\ReceiveController::class, 'index'])->middleware('apimid:receive:all');
        Route::post('/', [App\Http\Controllers\ReceiveController::class, 'store'])->middleware('apimid:receive:store');
        Route::patch('/', [App\Http\Controllers\ReceiveController::class, 'update'])->middleware('apimid:receive:update');
        Route::get('/show', [App\Http\Controllers\ReceiveController::class, 'show'])->middleware('apimid:receive:show');
    });

    Route::group(['prefix' => 'mutation'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\MutationController::class, 'index'])->middleware('apimid:mutation:all');
        Route::post('/', [App\Http\Controllers\MutationController::class, 'store'])->middleware('apimid:mutation:store');
        Route::patch('/', [App\Http\Controllers\MutationController::class, 'update'])->middleware('apimid:mutation:update');
        Route::delete('/item', [App\Http\Controllers\MutationController::class, 'destroyItem'])->middleware('apimid:mutation:destroy');
        Route::get('/show', [App\Http\Controllers\MutationController::class, 'show'])->middleware('apimid:mutation:show');
    });

    Route::group(['prefix' => 'gudang'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\GudangController::class, 'index'])->middleware('apimid:gudang:all');
        Route::get('/show', [App\Http\Controllers\GudangController::class, 'show'])->middleware('apimid:gudang:show');
    });

    Route::group(['prefix' => 'retur'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\ReturController::class, 'index'])->middleware('apimid:retur:all');
        Route::post('/', [App\Http\Controllers\ReturController::class, 'store'])->middleware('apimid:retur:store');
        Route::get('/show', [App\Http\Controllers\ReturController::class, 'show'])->middleware('apimid:retur:show');
    });

    Route::group(['prefix' => 'inventory'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\InventoryController::class, 'index'])->middleware('apimid:inventory:all');
        Route::patch('/unit', [App\Http\Controllers\InventoryController::class, 'updateByUnit'])->middleware('apimid:inventory:updateUnit');
    });

    Route::group(['prefix' => 'potype'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\PotypeController::class, 'index'])->middleware('apimid:potype:all');
        Route::post('/', [App\Http\Controllers\PotypeController::class, 'store'])->middleware('apimid:potype:store');
        Route::patch('/', [App\Http\Controllers\PotypeController::class, 'update'])->middleware('apimid:potype:update');
        Route::delete('/', [App\Http\Controllers\PotypeController::class, 'destroy'])->middleware('apimid:potype:destroy');
        Route::get('/show', [App\Http\Controllers\PotypeController::class, 'show'])->middleware('apimid:potype:show');
    });

    Route::group(['prefix' => 'item'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\ItemController::class, 'index'])->middleware('apimid:item:all');
        Route::post('/', [App\Http\Controllers\ItemController::class, 'store'])->middleware('apimid:item:store');
        Route::patch('/', [App\Http\Controllers\ItemController::class, 'update'])->middleware('apimid:item:update');
        Route::delete('/', [App\Http\Controllers\ItemController::class, 'destroy'])->middleware('apimid:item:destroy');
        Route::get('/show', [App\Http\Controllers\ItemController::class, 'show'])->middleware('apimid:item:show');
    });

    Route::group(['prefix' => 'sediaan'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\SediaanController::class, 'index'])->middleware('apimid:sediaan:all');
        Route::post('/', [App\Http\Controllers\SediaanController::class, 'store'])->middleware('apimid:sediaan:store');
        Route::patch('/', [App\Http\Controllers\SediaanController::class, 'update'])->middleware('apimid:sediaan:update');
        Route::delete('/', [App\Http\Controllers\SediaanController::class, 'destroy'])->middleware('apimid:sediaan:destroy');
        Route::get('/show', [App\Http\Controllers\SediaanController::class, 'show'])->middleware('apimid:sediaan:show');
    });

    Route::group(['prefix' => 'pos-inventory'], function () use ($router) {
        Route::get('/', [App\Http\Controllers\PosInventoryController::class, 'index'])->middleware('apimid:posInventory:all');
        Route::post('/', [App\Http\Controllers\PosInventoryController::class, 'store'])->middleware('apimid:posInventory:store');
        Route::patch('/', [App\Http\Controllers\PosInventoryController::class, 'update'])->middleware('apimid:posInventory:update');
        Route::delete('/', [App\Http\Controllers\PosInventoryController::class, 'destroy'])->middleware('apimid:posInventory:destroy');
        Route::get('/show', [App\Http\Controllers\PosInventoryController::class, 'show'])->middleware('apimid:posInventory:show');
    });

});
