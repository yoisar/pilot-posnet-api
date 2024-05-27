<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\PaymentController;

Route::post('/register-card', [CardController::class, 'registerCard']);
Route::post('/do-payment', [PaymentController::class, 'doPayment']);
