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
    public function showAll($botId): JsonResponse
    {
        if($bot = Bot::find($botId)){
            if($bot->messages->count() > 0){
                $messages = $bot->messages->sortBy('id');
                return ResponseService::success(
                    MessageResource::collection($messages)
                );
            }
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
        $attachments = null;
        if(isset($messageParams['attachments'])){
            $attachments = $messageParams['attachments'];
            unset($messageParams['attachments']);
        }

        if($messageParams['type'] == "question"){
            $messageParams['need_confirmation'] = true;
        }

        $newMessage = Message::create($messageParams);
        if(isset($messageParams['buttons'])){
            $keyboard = $newMessage->keyboard;
            $keyboard->buttons()->sync($messageParams['buttons']);
        }
        if(!is_null($attachments)){
            $newMessage->attachment()->sync($attachments);
        }
        return ResponseService::success(
            new MessageResource($newMessage)
        );
    }

    /**
     * @param string $messageId
     * @param array $validated
     * @return JsonResponse
     */
    public function update(string $messageId, array $validated): JsonResponse
    {
        $attachments = null;
        if(isset($validated['attachments'])){
            $attachments = $validated['attachments'];
            unset($validated['attachments']);
        }
        $message = Message::findOrFail($messageId);
        if ($message && $message->update($validated)) {

            if(!is_null($attachments)){
                $message->attachment()->sync($attachments);
            }
            if(isset($validated['buttons'])){
                $keyboard = $message->keyboard;
                $keyboard->buttons()->sync($validated['buttons']);
            }
            return ResponseService::success(new MessageResource($message));
        }
        return ResponseService::notFound();
    }

    /**
     * @param string $messageId
     * @return JsonResponse
     */
    public function destroy(string $messageId): JsonResponse
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
