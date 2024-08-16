<?php

namespace Valibool\TelegramConstruct\Services\Object;

use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Models\User;
use Valibool\TelegramConstruct\Services\Output;
use Valibool\TelegramConstruct\Services\ValidationService;

class InputCallbackQuery
{
    public function __construct($telegram, $inputCallbackQuery, Bot $bot)
    {
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->pressedButton = $inputCallbackQuery->callback_query['data'];
        $this->from = $inputCallbackQuery->callback_query['from'];
        $this->validationService = new ValidationService($this->telegram);

    }

    public function initUser(): void
    {
        $this->user = User::where('tg_user_id', $this->from['id'])->firstOrCreate([
            'tg_user_id' => $this->from['id'],
            'tg_user_name' => $this->from['username'] ?? null,
            'name' => $this->from['username'] ?? 'noname [' . $this->from['id'] . ']',
        ]);
    }

    public function getAnswer(): void
    {
        $this->initUser();
        $this->parsePressedButton();
//        if ($this->constructMessage) {
//            $keyboard = Output::renderInlineKeyboardByMessage($this->constructMessage);
//            $answer = Output::sendMessage($this->telegram, $this->constructMessage->text, $keyboard, $this->from['id']);
//            $this->user->last_message_id = $this->constructMessage->id;
//            $this->user->last_tg_message_id = $answer->message_id;
//            $this->user->save();
//        }
    }

    private function getMessageByPressedButton(): Message|null
    {
        return Message::find($this->pressedButton);
    }

    private function parsePressedButton(): void
    {
        if (str_contains($this->pressedButton, 'confirmation')) {
            if (explode('_', $this->pressedButton)[1]) {
                $this->validationService->confirmLastMessage($this->user);
                $this->constructMessage = $this->user->lastQuestion->nextMessage;
            }
        } else {
//            $this->constructMessage = $this->getMessageByPressedButton();
            $this->checkMessage();
        }
    }

    private function checkMessage()
    {
        $this->message = $this->user->lastQuestion;

        switch ($this->message->type){
            case 'modelMessage':

                if($this->message->nextMessage->type == 'modelMessage'){

                    $nowModelClass = $this->message->keyboard->model_class;

                    $modelNextMessage = app($this->message->nextMessage->keyboard->model_class);

                    if (isset($modelNextMessage::$filterFieldsForTgButtons)){
                        $relationKey = $modelNextMessage::$filterFieldsForTgButtons[$nowModelClass];
                        $filter = [
                            'key' =>$relationKey,
                            'value' =>$this->pressedButton,
                        ];
                        $keyboard = Output::renderInlineKeyboardByDynamicKeyboard($this->message->nextMessage->keyboard, $filter);
                        $answer = Output::sendMessage($this->telegram, $this->message->nextMessage->text, $keyboard, $this->from['id']);
                        $this->user->last_message_id = $this->message->id;
                        $this->user->last_tg_message_id = $answer->message_id;
                        $this->user->save();
                    }
                }
                break;

            case 'message':
                $this->constructMessage = $this->getMessageByPressedButton();
                $keyboard = Output::renderInlineKeyboardByMessage($this->constructMessage);
                $answer = Output::sendMessage($this->telegram, $this->constructMessage->text, $keyboard, $this->from['id']);
                $this->user->last_message_id = $this->constructMessage->id;
                $this->user->last_tg_message_id = $answer->message_id;
                $this->user->save();
                break;
        }
    }
}
