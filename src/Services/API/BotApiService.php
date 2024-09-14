<?php

namespace Valibool\TelegramConstruct\Services\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Valibool\TelegramConstruct\Http\Resources\BotResource;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Services\Messages\MessageConstructor;
use Valibool\TelegramConstruct\Services\Response\ResponseService;
use Valibool\TelegramConstruct\Services\TelegramCommands;

class BotApiService
{
    /**
     * @throws TelegramSDKException
     */
    public static function connectBot($token): JsonResponse
    {
        $data = [];
        $telegram = new Api($token);
        $response = $telegram->getMe();
        $botParamsRequest = $response->toArray();

        if ($botParamsRequest) {
            $data['secret_token'] = Str::orderedUuid()->toString();
            $data['token'] = $token;
            $data['name'] = $botParamsRequest['first_name'];
            $data['first_name'] = $botParamsRequest['first_name'];
            $data['user_name'] = $botParamsRequest['username'];
            $data['permissions'] = [
                'can_join_groups' => $botParamsRequest['can_join_groups'],
                'can_read_all_group_messages' => $botParamsRequest['can_read_all_group_messages'],
                'supports_inline_queries' => $botParamsRequest['supports_inline_queries'],
                'can_connect_to_business' => $botParamsRequest['can_connect_to_business'],
            ];
//            $data['user_id'] = Auth::id();
            $data['user_id'] = 1;
            if ($bot = Bot::create($data)) {
                MessageConstructor::createStartDefaultMessage($bot);
                return ResponseService::success(new BotResource($bot));
            }
            return ResponseService::unSuccess();
        }
        return ResponseService::notFound();
    }

    /**
     * @param array $validated
     * @return JsonResponse
     * @throws TelegramSDKException
     */
    public static function setWebhook(array $validated): JsonResponse
    {
        $bot = Bot::find($validated['bot_id']);
        $telegram = new Api($bot->token);
        if ($webhook = TelegramCommands::setWebhook($telegram, $validated['url'], $bot)) {
            return ResponseService::success(new BotResource($bot));
        }
        return ResponseService::unSuccess($webhook);
    }
}
