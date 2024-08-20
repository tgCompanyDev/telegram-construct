<?php

use Illuminate\Support\Facades\Route;
use Valibool\TelegramConstruct\Http\Controllers\BotController;
use Valibool\TelegramConstruct\Http\Controllers\MessageController;
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

Route::prefix('api/bot/')->group(function () {
    Route::post('connect-bot', [BotController::class, 'connectBot']);
    Route::post('set-webhook', [BotController::class, 'setWebhook']);
});

Route::prefix('api/tg-construct/')->group(function () {
    Route::apiResource('message', MessageController::class);
    Route::post('message/{message_id}/sync-buttons', [MessageController::class, 'syncButtons']);
    Route::post('message/confirm-save-all', [MessageController::class, 'confirmSaveAll']);
});
