<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotifyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('verify.email');
Route::post('/resend-otp', [AuthController::class, 'resendOTP'])->name('resend.emali');
Route::post('/forgot-password',[AuthController::class, 'forgotPassword'])->name('forgot.password');
Route::post('/reset-password',[AuthController::class, 'resetPassword'])->name('reset.password');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/update', [AuthController::class, 'updateProfile']);
    Route::delete('/delete', [AuthController::class, 'deleteAccount']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    //Notification routes
    Route::get('/all-notification', [NotifyController::class, 'all'])->name('all.notification');
    Route::get('/unread-notification', [NotifyController::class, 'unread'])->name('unread.notification');
    Route::get('/read-notification', [NotifyController::class, 'read'])->name('read.notification');
    Route::post('mark-notification/{id}', [NotifyController::class, 'markAsRead'])->name('mark.notification');
    Route::post('/mark-all-notification', [NotifyController::class, 'markAllAsRead'])->name('mark.all.notification');
});