<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

Route::middleware('auth:sanctum')->group(function() {
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/profile", [PageController::class, "profile"]);
    Route::get("/transaction", [PageController::class, "transaction"]);
    Route::get("/transaction/{trx_id}", [PageController::class, "transactionDetail"]);
    Route::get("/notification", [PageController::class, "notification"]);
    Route::get("/notification/{id}", [PageController::class, "notificationDetail"]);
    Route::get("/check-phone", [PageController::class, "checkPhone"]);
    Route::get("/transfer/confirm", [PageController::class, "confirmTransfer"]);
    Route::post("/transfer/complete", [PageController::class, "completeTransfer"]);
    Route::get('/scan-and-pay-form',[PageController::class, "scanAndPayForm"]);
    Route::get('/scan-and-pay/confirm',[PageController::class, "scanAndPayConfrim"]);
    Route::post('/scan-and-pay/complete',[PageController::class, "scanAndPayComplete"]);

});
