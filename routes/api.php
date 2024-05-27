<?php
use App\Http\Controllers\PosnetController;
use Illuminate\Routing\Route;

Route::post('/register-card', [PosnetController::class, 'registerCard']);
Route::post('/do-payment', [PosnetController::class, 'doPayment']);
