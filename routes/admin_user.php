<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\WalletController;
use App\Http\Controllers\Backend\AdminUserController;

Route::prefix('admin')->name('admin.')->middleware('auth:admin_user')->group(function() {
    Route::get("/", [PageController::class, "home"])->name("home");

    Route::resource('admin-user', AdminUserController::class);
    Route::get("admin-user/datatable/ssd", [AdminUserController::class, "ssd"]);

    Route::resource('user', UserController::class);
    Route::get("user/datatable/ssd", [UserController::class, "ssd"]);

    Route::get("wallet", [WalletController::class, "index"])->name("wallet.index");
    Route::get("wallet/datatable/ssd", [WalletController::class, "ssd"]);

    Route::get('wallet/add-amount', [WalletController::class, "addAmount"])->name("wallet.add_amount");
    Route::post('wallet/add-amount/store', [WalletController::class, "addAmountStore"])->name('wallet.add_amount.store');

    Route::get('wallet/reduce-amount', [WalletController::class, "reduceAmount"])->name("wallet.reduce_amount");
    Route::post('wallet/reduce-amount/store', [WalletController::class, "reduceAmountStore"])->name("wallet.reduce_amount.store");

});
