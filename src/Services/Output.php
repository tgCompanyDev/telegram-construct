<?php

namespace Valibool\TelegramConstruct\Services;

use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard as SDKKeyboard;
use Telegram\Bot\Objects\Message;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;

class Output
{

    private SDKKeyboard $keyboard;
    private Api $client;
    private string $messageText;
    private string $chatId;
    private ?TgConstructAttachment $photo = null;

    /**
     * @param $customButtons
     * @return SDKKeyboard
     */
    public function setCustomKeyboard($customButtons): SDKKeyboard
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

        return $this->keyboard = $keyboard;
    }
    /**
     * @param SDKKeyboard $keyboard
     * @return SDKKeyboard
     */
    public function setKeyboard(SDKKeyboard $keyboard): SDKKeyboard
    {
        return $this->keyboard = $keyboard;
    }

    /**
     * @param Api $client
     * @return Api
     */
    public function setClient(Api $client): Api
    {
        return $this->client = $client;
    }

    /**
     * @param string $messageText
     * @return string
     */
    public function setMessageText(string $messageText)
    {
        return $this->messageText = $messageText;
    }

    /**
     * @param string $chatId
     * @return string
     */
    public function setChatId(string $chatId): string
    {
        return $this->chatId = $chatId;
    }

    public function setPhoto(TgConstructAttachment $photo): string
    {
        return $this->photo = $photo;
    }

    /**
     * @return Message
     * @throws TelegramSDKException
     */
    public function sendMessage(): Message
    {
        if ($this->photo) {
            return $this->sendPhoto();
        } else {
            return $this->sendTextMessage();
        }
    }

    /**
     * @return Message
     * @throws TelegramSDKException
     */
    public function sendTextMessage(): Message
    {
        return $this->client->sendMessage([
            'chat_id' => $this->chatId,
            'text' => $this->messageText,
            'reply_markup' => $this->keyboard ?? false
        ]);
    }


    public function deleteMessage($messageId)
    {
        return $this->client->deleteMessage([
            'chat_id' => $this->chatId,
            'message_id' => $messageId,
        ]);
    }

    /**
     * @return Message
     * @throws TelegramSDKException
     */
    public function sendPhoto(): Message
    {
        $file = InputFile::create(Storage::getConfig()['root'] . '/' . $this->photo->disk . '/' . $this->photo->physicalPath(), $this->photo->name);

        return $this->client->sendPhoto([
            'chat_id' => $this->chatId,
            'photo' => $file,
            'caption' => $this->messageText,
            'reply_markup' => $this->keyboard
        ]);
    }


    public static function sendFreeMessage($telegram, $text, $keyboard, $chatId)
    {
        return $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'reply_markup' => $keyboard
        ]);
    }

//    public static function sendPhoto($telegram, $text, $keyboard, $chatId, $photo): mixed
//    {
//        $file = InputFile::create(Storage::getConfig()['root'].'/'.$photo->disk.'/'.$photo->physicalPath(),$photo->name);
//
//        return $telegram->sendPhoto([
//            'chat_id' => $chatId,
//            'photo' => $file,
//            'caption' => $text,
//            'reply_markup' => $keyboard
//        ]);
//    }

//    public static function editMessageText($telegram, $text, $keyboard, $chatId, $messageId)
//    {
//        return $telegram->editMessageText([
//            'chat_id' => $chatId,
//            'message_id' => $messageId,
//            'text' => $text,
//            'reply_markup' => $keyboard
//        ]);
//    }
//
//    public static function editMessageCaption($telegram, $text, $keyboard, $chatId, $messageId)
//    {
//        return $telegram->editMessageCaption([
//            'chat_id' => $chatId,
//            'message_id' => $messageId,
//            'caption' => $text,
//            'reply_markup' => $keyboard
//        ]);
//    }
//


//    public static function renderInlineKeyboardByMessage(Message $message): bool|SDKKeyboard
//    {
//        $keyboard = false;
//        if($message->buttons){
//            $buttons = [];
//            $keyboard = new SDKKeyboard(['resize_keyboard' => $message->keyboard->resize_keyboard, 'one_time_keyboard' => $message->keyboard->one_time_keyboard]);
//            $keyboard->inline();
//            foreach ($message->buttons as $button) {
//                if (isset($button['callback_data'])) {
//                    $buttons[] = SDKKeyboard::inlineButton(['text' => $button['text'], 'callback_data' => $button['callback_data']]);
//                }
//            }
//            $keyboard->row($buttons);
//        }
////        if($message->keyboard->isDynamic()){
////            $model = $message->keyboard->connectedModel()->all();
////            $connectedModelParams = TelegramService::ModelsForDialogButtons();
////            $modelKeyToTextButton = $connectedModelParams[$message->keyboard->model_class]['keyToNameButton'];
////            $buttons = [];
////            $keyboard = new Keyboard(['resize_keyboard' => $message->keyboard->resize_keyboard, 'one_time_keyboard' => $message->keyboard->one_time_keyboard]);
////            $keyboard->inline();
////            foreach ($model as $item) {
////                $buttons[] = Keyboard::inlineButton([
////                    'text' => $item->{$modelKeyToTextButton},
////                    'callback_data' => $item->id]);
////            }
////            $keyboard->row($buttons);
////        }
//        return $keyboard;
//    }


//    public static function renderInlineKeyboardByDynamicKeyboard(Keyboard $keyboardMessage, $filter = false): bool|SDKKeyboard
//    {
//        $keyboard = false;
//        if($keyboardMessage->isDynamic()){
//            $model = $keyboardMessage->connectedModel();
//
//            if($filter){
//                $model = $model->where($filter['key'], $filter['value'])->get();
//            } else {
//                $model = $model->all();
//            }
//
//            $connectedModelParams = TelegramService::ModelsForDialogButtons();
//            $modelKeyToTextButton = $connectedModelParams[$keyboardMessage->model_class]['keyToNameButton'];
//            $buttons = [];
//            $keyboard = new SDKKeyboard(['resize_keyboard' => $keyboardMessage->resize_keyboard, 'one_time_keyboard' => $keyboardMessage->one_time_keyboard]);
//            $keyboard->inline();
//            foreach ($model as $item) {
//                $buttons[] = SDKKeyboard::inlineButton([
//                    'text' => $item->{$modelKeyToTextButton},
//                    'callback_data' => $item->id]);
//            }
//            $keyboard->row($buttons);
//        }
//        return $keyboard;
//    }

//    public static function renderInlineKeyboardCustom($customButtons): SDKKeyboard
//    {
//        $buttons = [];
//        $keyboard = new SDKKeyboard(['resize_keyboard' => true, 'one_time_keyboard' => true]);
//        $keyboard->inline();
//        foreach ($customButtons as $button) {
//            if (isset($button['callback_data'])) {
//                $buttons[] = SDKKeyboard::inlineButton(['text' => $button['text'], 'callback_data' => $button['callback_data']]);
//            }
//        }
//
//        $keyboard->row($buttons);
//
//        return $keyboard;
//    }
}
