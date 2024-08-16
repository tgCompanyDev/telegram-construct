<?php

use Illuminate\Support\Facades\Route;
use Valibool\TelegramConstruct\Http\Controllers\TelegramConstructController;

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

Route::post('/telegram-input', [TelegramConstructController::class, 'input']);

