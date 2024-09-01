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
     *
     * @OA\Post(
     *               path="/api/bot/connect-bot",
     *               operationId="bot/connect-bot",
     *               tags={"Бот"},
     *               description="Подключение бота",
     *                  security = {
     *                 {"apiKey": {}},
     *                },
     *
     *               @OA\RequestBody(
     *                  @OA\MediaType(
     *                      mediaType="application/json",
     *                      @OA\Schema(
     *
     *                         @OA\Property(
     *                              property="token",
     *                              type="string"
     *                          ),
     *
     *                          example={
     *                            "token": "6685188155:AAFfQxYZBwyC3mF-VfuKt6Nr9M-TtgIsa9s",
     *                          }
     *                      )
     *                  )
     *              ),
     *               @OA\Response(response="200",
     *                    description="",
     *                    @OA\MediaType(
     *                        mediaType="application/json",
     *                        @OA\Schema(
     *                        ),
     *                    ),
     *                ),
     *           )
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
     *
     * @OA\Post(
     *                path="/api/bot/set-webhook",
     *                operationId="bot/set-webhook",
     *                tags={"Бот"},
     *                description="Подключение вебхука",
     *                   security = {
     *                  {"apiKey": {}},
     *                 },
     *
     *                @OA\RequestBody(
     *                   @OA\MediaType(
     *                       mediaType="application/json",
     *                       @OA\Schema(
     *
     *                          @OA\Property(
     *                               property="url",
     *                               type="string"
     *                           ),
     *                           @OA\Property(
     *                               property="bot_id",
     *                               type="integer"
     *                           ),
     *
     *                           example={
     *                             "url": "https://webhook-url.loc",
     *                             "bot_id": 1,
     *                           }
     *                       )
     *                   )
     *               ),
     *                @OA\Response(response="200",
     *                     description="",
     *                     @OA\MediaType(
     *                         mediaType="application/json",
     *                         @OA\Schema(
     *                         ),
     *                     ),
     *                 ),
     *            )
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
