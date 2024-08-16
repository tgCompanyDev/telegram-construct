<?php

namespace Valibool\TelegramConstruct\Services;

use Telegram\Bot\Keyboard\Keyboard as SDKKeyboard;
use Valibool\TelegramConstruct\Models\Keyboard;
use Valibool\TelegramConstruct\Models\Message;

class Output
{
    public static function sendMessage($telegram, $message, $keyboard, $chatId)
    {
        return $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
            'reply_markup' => $keyboard
        ]);
    }

//    public static function editMessageText($telegram, $message, $keyboard, $chatId)
//    {
//        return $telegram->editMessageText([
//            'chat_id' => $chatId,
//            'text' => $message,
//            'reply_markup' => $keyboard
//        ]);
//    }

    public static function renderInlineKeyboardByMessage(Message $message)
    {
        $keyboard = false;
        if($message->buttons){
            $buttons = [];
            $keyboard = new SDKKeyboard(['resize_keyboard' => $message->keyboard->resize_keyboard, 'one_time_keyboard' => $message->keyboard->one_time_keyboard]);
            $keyboard->inline();
            foreach ($message->buttons as $button) {
                if (isset($button['callback_data'])) {
                    $buttons[] = SDKKeyboard::inlineButton(['text' => $button['text'], 'callback_data' => $button['callback_data']]);
                }
            }
            $keyboard->row($buttons);
        }
//        if($message->keyboard->isDynamic()){
//            $model = $message->keyboard->connectedModel()->all();
//            $connectedModelParams = TelegramService::ModelsForDialogButtons();
//            $modelKeyToTextButton = $connectedModelParams[$message->keyboard->model_class]['keyToNameButton'];
//            $buttons = [];
//            $keyboard = new Keyboard(['resize_keyboard' => $message->keyboard->resize_keyboard, 'one_time_keyboard' => $message->keyboard->one_time_keyboard]);
//            $keyboard->inline();
//            foreach ($model as $item) {
//                $buttons[] = Keyboard::inlineButton([
//                    'text' => $item->{$modelKeyToTextButton},
//                    'callback_data' => $item->id]);
//            }
//            $keyboard->row($buttons);
//        }
        return $keyboard;
    }
    public static function renderInlineKeyboardByDynamicKeyboard(Keyboard $keyboardMessage, $filter = false)
    {
        $keyboard = false;
        if($keyboardMessage->isDynamic()){
            $model = $keyboardMessage->connectedModel();

            if($filter){
                $model = $model->where($filter['key'], $filter['value'])->get();
            } else {
                $model = $model->all();
            }

            $connectedModelParams = TelegramService::ModelsForDialogButtons();
            $modelKeyToTextButton = $connectedModelParams[$keyboardMessage->model_class]['keyToNameButton'];
            $buttons = [];
            $keyboard = new SDKKeyboard(['resize_keyboard' => $keyboardMessage->resize_keyboard, 'one_time_keyboard' => $keyboardMessage->one_time_keyboard]);
            $keyboard->inline();
            foreach ($model as $item) {
                $buttons[] = SDKKeyboard::inlineButton([
                    'text' => $item->{$modelKeyToTextButton},
                    'callback_data' => $item->id]);
            }
            $keyboard->row($buttons);
        }
        return $keyboard;
    }
    public static function renderInlineKeyboardCustom($customButtons)
    {
        $buttons = [];
        $keyboard = new SDKKeyboard(['resize_keyboard' => true, 'one_time_keyboard' => true]);
        $keyboard->inline();
        foreach ($customButtons as $button) {
            if (isset($button['callback_data'])) {
                $buttons[] = SDKKeyboard::inlineButton(['text' => $button['text'], 'callback_data' => $button['callback_data']]);
            }
        }

        $keyboard->row($buttons);

        return $keyboard;
    }
}
