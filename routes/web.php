<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// user auth
Auth::routes();

// admin auth
Route::get('/admin/login', [AdminLoginController::class, "showLoginForm"]);
Route::post('/admin/login', [AdminLoginController::class, "login"])->name('admin.login');
Route::post('/admin/logout', [AdminLoginController::class, "logout"])->name('admin.logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [PageController::class, "home"])->name('home');
    Route::get('/profile', [PageController::class, "profile"])->name('profile');
    Route::get('/update-password', [PageController::class, "updatePassword"])->name('update-password');
    Route::post('/update-password', [PageController::class, "updatePasswordStore"])->name('update-password.store');

    Route::get('/wallet', [PageController::class, "wallet"])->name('wallet');
    Route::get('/transfer', [PageController::class, "transfer"])->name('transfer');
    Route::get("/transfer-hash", [PageController::class, "transferHash"]);
    Route::get('/transfer/confirm', [PageController::class, "confirmTransfer"])->name('confirm-transfer');
    Route::post('/transfer/complete', [PageController::class, "completeTransfer"])->name('complete-transfer');

    Route::get('/check-phone', [PageController::class, "checkPhone"]);
    Route::get('/check-password', [PageController::class, "checkPassword"]);

    Route::get('/transaction', [PageController::class, "transaction"])->name('transaction');
    Route::get('/transaction-detail/{trx_id}', [PageController::class, "transactionDetail"])->name('transaction-detail');

    Route::get('/receive-qr',[PageController::class, "receiveQR"])->name('receive-qr');

    Route::get('/scan-and-pay',[PageController::class, "scanAndPay"])->name('scan-and-pay');
    Route::get('/scan-and-pay-form',[PageController::class, "scanAndPayForm"])->name('scan-and-pay-form');
    Route::get('/scan-and-pay/confirm',[PageController::class, "scanAndPayConfrim"])->name('scan-and-pay-confirm');
    Route::post('/scan-and-pay/complete',[PageController::class, "scanAndPayComplete"])->name('scan-and-pay-complete');

    Route::get('/notification', [NotificationController::class, "index"])->name('notification');
    Route::get('/notification/{id}', [NotificationController::class, "show"])->name('notification-detail');
    Route::delete('/notification/{id}', [NotificationController::class, "destory"]);
});
