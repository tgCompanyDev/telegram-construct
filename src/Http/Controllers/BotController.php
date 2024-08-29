<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Valibool\TelegramConstruct\Services\API\BotApiService;


class BotController extends Controller
{

    public function __construct()
    {
    }

    /**
     * @throws TelegramSDKException
     */
    public function connectBot(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|unique:bots',
        ]);

        return BotApiService::connectBot($validated['token']);
    }

    /**
     * @throws TelegramSDKException
     */
    public function setWebhook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'bot_id' => 'required|exists:bots,id',
        ]);

        return BotApiService::setWebhook($validated);
    }
}
