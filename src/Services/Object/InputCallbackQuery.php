<?php

namespace Valibool\TelegramConstruct\Services\Object;

use Telegram\Bot\Exceptions\TelegramSDKException;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Models\TgUser;
use Valibool\TelegramConstruct\Services\MessageConstructService;
use Valibool\TelegramConstruct\Services\Output;
use Valibool\TelegramConstruct\Services\TgUserService;
use Valibool\TelegramConstruct\Services\ValidationService;

class InputCallbackQuery
{
    private mixed $telegram;
    private Bot $bot;
    private mixed $pressedButton;
    private mixed $from;
    private ValidationService $validationService;
    private TgUser $user;
    private MessageConstructService $messageService;

    public function __construct($telegram, $inputCallbackQuery, Bot $bot)
    {
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->pressedButton = $inputCallbackQuery->callback_query['data'];
        $this->from = $inputCallbackQuery->callback_query['from'];
        $this->validationService = new ValidationService($this->telegram);
        $this->setUser();
    }

    /**
     * @return Output
     */
    public function generateAnswer(): Output
    {
        return $this->parsePressedButton();
    }

    /**
     * @throws TelegramSDKException
     */
    public function sendAnswer(): \Telegram\Bot\Objects\Message
    {
        $answer = $this->messageService->sendMessage();
        $this->user->saveLastMessage($this->messageService->outputMessage, $answer->message_id);

        return $answer;
    }

    /**
     * @return void
     */
    public function setUser(): void
    {
        $this->user = TgUserService::initUser($this->from);
    }

    /**
     * @return Message|null
     */
    private function getMessageByPressedButton(): Message|null
    {
        return Message::find($this->pressedButton);
    }

    /**
     * @return Output
     */
    private function parsePressedButton(): Output
    {
//        if (str_contains($this->pressedButton, 'confirmation')) {
//            if (explode('_', $this->pressedButton)[1]) {
//                $this->validationService->confirmLastMessage($this->user);
//                $this->constructMessage = $this->user->lastQuestion->nextMessage;
//            }
//        } else {
        return $this->checkMessage();
//        }
    }

    /**
     * @return Output
     */
    private function checkMessage(): Output
    {
        $this->message = $this->user->lastQuestion;

        switch ($this->message->type) {
//            case 'modelMessage':
//
//                if($this->message->nextMessage->type == 'modelMessage'){
//
//                    $nowModelClass = $this->message->keyboard->model_class;
//
//                    $modelNextMessage = app($this->message->nextMessage->keyboard->model_class);
//
//                    if (isset($modelNextMessage::$filterFieldsForTgButtons)){
//                        $relationKey = $modelNextMessage::$filterFieldsForTgButtons[$nowModelClass];
//                        $filter = [
//                            'key' =>$relationKey,
//                            'value' =>$this->pressedButton,
//                        ];
//                        $keyboard = Output::renderInlineKeyboardByDynamicKeyboard($this->message->nextMessage->keyboard, $filter);
//                        $answer = Output::sendMessage($this->telegram, $this->message->nextMessage->text, $keyboard, $this->from['id']);
//                        $this->user->last_message_id = $this->message->id;
//                        $this->user->last_tg_message_id = $answer->message_id;
//                        $this->user->save();
//                    }
//                }
//                break;

            case 'message':
                $outputMessage = $this->getMessageByPressedButton();

                $this->messageService = new MessageConstructService($this->telegram, $this->from['id'], $this->bot,);
                return $this->messageService->setOutputMessage($outputMessage);
//                $this->constructMessage = $this->getMessageByPressedButton();
//                $keyboard = Output::renderInlineKeyboardByMessage($this->constructMessage);
//                if(!$this->user->last_tg_message_id){
//                    $answer = Output::sendMessage($this->telegram, $this->constructMessage->text, $keyboard, $this->from['id']);
////                    $answer = Output::sendMessage($this->telegram, $this->constructMessage->text, $keyboard, '-1002198941062');
//                } else {
//                    $message = $this->user->lastQuestion;
//                    if($message->image) {
//                        if($this->constructMessage->image){
//                            $answer = Output::editMessageCaption($this->telegram, $this->constructMessage->text, $keyboard, $this->from['id'],$this->user->last_tg_message_id);
//                        } else {
//                            Output::deleteMessage($this->telegram, $this->from['id'], $this->user->last_tg_message_id);
//                            $answer = Output::sendMessage($this->telegram, $this->constructMessage->text, $keyboard, $this->from['id']);
//                        }
//                    } else {
//                        $answer = Output::editMessageText($this->telegram, $this->constructMessage->text, $keyboard, $this->from['id'],$this->user->last_tg_message_id);
//
//                    }
//                }
//                $this->user->saveLastMessage($this->constructMessage->id, $answer->message_id);
                break;
        }
    }
}
