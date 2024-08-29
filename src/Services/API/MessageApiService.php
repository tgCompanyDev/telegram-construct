<?php

namespace Valibool\TelegramConstruct\Services\API;

use Illuminate\Http\JsonResponse;
use Valibool\TelegramConstruct\Http\Resources\MessageResource;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Services\Response\ResponseService;

class MessageApiService
{
    /**
     * @return JsonResponse
     */
    public function showAll(): JsonResponse
    {
        $messages = Message::all()->sortBy('id');
        if ($messages) {
            return ResponseService::success(
                MessageResource::collection($messages)
            );
        }
        return ResponseService::notFound();
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $messageId): JsonResponse
    {
        $message = Message::find($messageId);
        if ($message) {
            return ResponseService::success(
                new MessageResource($message)
            );
        }
        return ResponseService::notFound();
    }

    /**
     * @param $messageParams
     * @return JsonResponse
     */
    public function store($messageParams): JsonResponse
    {
        $newMessage = Message::create($messageParams);
        if(isset($messageParams['buttons'])){
            $keyboard = $newMessage->keyboard;
            $keyboard->buttons()->sync($messageParams['buttons']);
        }
        return ResponseService::success(
            new MessageResource($newMessage)
        );
    }

    /**
     * @param int $messageId
     * @param array $validated
     * @return JsonResponse
     */
    public function syncButtons(int $messageId, array $validated): JsonResponse
    {
        $message = Message::find($messageId);
        if ($message && $message->keyboard) {
            $keyboard = $message->keyboard;
            if ($keyboard->buttons()->sync($validated['buttons'])) {
                if ($message->next_message_id)
                    $message->next_message_id = null;
                $message->save();
                return ResponseService::success(new MessageResource($message));
            }
        }
        return ResponseService::notFound();
    }

    /**
     * @param string $messageId
     * @param array $validated
     * @return JsonResponse
     */
    public function update(string $messageId, array $validated): JsonResponse
    {
        $message = Message::findOrFail($messageId);
        if ($message && $message->update($validated)) {
            if(isset($validated['buttons'])){
                $keyboard = $message->keyboard;
                $keyboard->buttons()->sync($validated['buttons']);
            }
            return ResponseService::success(new MessageResource($message));
        }
        return ResponseService::notFound();
    }

    /**
     * @param int $botId
     * @return JsonResponse
     */
    public function confirmSaveAll(int $botId): JsonResponse
    {
        if($bot = Bot::find($botId)){
            foreach ($bot->messages as $message) {
                if(!$message->save_confirmation){
                    $message->save_confirmation = true;
                    $message->save();
                }
            }
            return ResponseService::success(MessageResource::collection($bot->messages));
        }
        return ResponseService::notFound();
    }

    public function destroy(string $messageId)
    {
        if($message = Message::find($messageId)){
            if($message->delete()){
                return ResponseService::success();
            }
            return ResponseService::unSuccess();
        }
        return ResponseService::notFound();
    }
}
