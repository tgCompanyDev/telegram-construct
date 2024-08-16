<?php

namespace Valibool\TelegramConstruct\Services\Object;

use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Models\User;
use Valibool\TelegramConstruct\Services\Output;
use Valibool\TelegramConstruct\Services\ValidationService;

class InputMessage
{
    public function __construct($telegram, $inputMessage, Bot $bot)
    {
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->firstMessage = $this->getFirstMessage();
        $this->from = $inputMessage->message['from'];
        $this->inputMessage = $inputMessage;
        $this->validationService = new ValidationService($telegram);
    }

    public function initUser()
    {
        $this->user = User::where('tg_user_id', $this->from['id'])->firstOrCreate([
            'tg_user_id' => $this->from['id'],
            'tg_user_name' => $this->from['username'] ?? null,
            'name' => $this->from['username'] ?? 'noname [' . $this->from['id'] . ']',
        ]);
    }

    public function getAnswer()
    {
        $this->initUser();
        return $this->generateAnswer();
    }

    public function generateAnswer()
    {
        if ($this->user->mustAnswer()) {
            $this->validateInput();
        } else {
            return $this->sendMessage($this->firstMessage);
        }
    }

    public function sendNextMessage()
    {
        if($nextMessage = Message::find($this->user->last_message_id)->nextMessage){
            return $this->sendMessage($nextMessage);
        }
        return null;
    }

    public function sendMessage(Message $message)
    {

        $keyboard = Output::renderInlineKeyboardByMessage($message);
        $answer = Output::sendMessage($this->telegram, $message->text, $keyboard, $this->from['id']);
        $this->user->last_message_id = $message->id;
        $this->user->last_tg_message_id = $answer->message_id;
        $this->user->save();
        if ($message->canSendNextMessage()) {
            $answer = $this->sendNextMessage();
        }

        return $answer;
    }

    private function getFirstMessage(): Message|null
    {
        return Message::where('first_message', true)->where('bot_id',$this->bot->id)->first();
    }

    private function validateInput()
    {
        $validatedFail = $this->validationService->validateAnswerTheQuestion(
            $this->inputMessage->getMessage()->text,
            $this->user->lastQuestion
        );

        if ($validatedFail) {
            if ($this->user->lastQuestion->need_confirmation) {
                return $this->validationService->sendValidationMessage($this->from['id']);
            }
        } else {
            return $this->validationService->sendConfirmationMessage(
                $this->inputMessage->getMessage()->text,
                $this->from['id'],
                $this->user->id
            );
        }
    }
}
