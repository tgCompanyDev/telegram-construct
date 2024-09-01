<?php

namespace Valibool\TelegramConstruct\Services\Object;

use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard as SDKKeyboard;
use Telegram\Bot\Objects\Update;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Models\TgUser;
use Valibool\TelegramConstruct\Services\MessageConstructService;
use Valibool\TelegramConstruct\Services\Output;
use Valibool\TelegramConstruct\Services\TgUserService;
use Valibool\TelegramConstruct\Services\ValidationService;

class InputMessage
{
    private TgUser $user;
    private Api $telegram;
    private Bot $bot;
    private ?Message $firstMessage;
    private mixed $from;
    private Update $inputMessage;
    private ValidationService $validationService;
    private SDKKeyboard|bool $keyboard;
    public MessageConstructService $messageService;

    public function __construct(Api $telegram, Update $inputMessage, Bot $bot)
    {
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->firstMessage = $this->getFirstMessage();
        $this->from = $inputMessage->message['from'];
        $this->inputMessage = $inputMessage;
        $this->validationService = new ValidationService($telegram);
        $this->setUser();
    }

    /**
     * @return Output
     */
    public function generateAnswer(): Output
    {
        if($this->user->lastQuestion    ){
            $outputMessage = $this->user->lastQuestion;
        } else {
            $outputMessage = $this->firstMessage;
        }
        $this->messageService = new MessageConstructService($this->telegram, $this->from['id'], $this->bot);
        return $this->messageService->setOutputMessage($outputMessage);


//        if ($this->user->mustAnswer()) {
//            $this->validateInput();
//        } else {
//            return $this->sendMessage($this->firstMessage);
//            return $this->firstMessage;
//        }
    }

    /**
     * @return \Telegram\Bot\Objects\Message
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function sendAnswer(): \Telegram\Bot\Objects\Message
    {
        $answer = $this->messageService->sendMessage();
        $this->user->saveLastMessage($this->messageService->outputMessage, $answer->message_id);

        if ($this->messageService->outputMessage->canSendNextMessage()) {
            if($nextMessageSent = $this->sendNextMessage()){
                $answer = $nextMessageSent;
            }
        }
        return $answer;
    }

    /**
     * @return void
     */
    protected function setUser(): void
    {
        $this->user = TgUserService::initUser($this->from);
    }


    /**
     * @return \Telegram\Bot\Objects\Message|null
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function sendNextMessage(): ?\Telegram\Bot\Objects\Message
    {
        if($this->user->last_message_id){
            if($nextMessage = Message::find($this->user->last_message_id)->nextMessage){
                $this->messageService->setOutputMessage($nextMessage);
                return $this->sendAnswer();
            }
        }
        return null;
    }

    /**
     * @return Message|null
     */
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
