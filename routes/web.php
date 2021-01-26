<?php

use Illuminate\Support\Facades\Route;
use M1guelpf\LoginWithApple\Http\Controllers\LoginWithAppleController;

Route::middleware(['guest'])->group(function () {
    Route::get('login/apple', [LoginWithAppleController::class, 'redirect'])->name('login.apple');
    Route::post('login/apple', [LoginWithAppleController::class, 'login']);
});
