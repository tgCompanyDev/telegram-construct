<?php

namespace Valibool\TelegramConstruct\Services;

use Illuminate\Http\Request;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Services\Messages\Input\InputCallbackQuery;
use Valibool\TelegramConstruct\Services\Messages\Input\InputTextMessage;
use Valibool\TelegramConstruct\Services\Messages\Message;
use Valibool\TelegramConstruct\Services\Messages\MessageConstructor;
use Valibool\TelegramConstruct\Services\Messages\MessageDB;
use Valibool\TelegramConstruct\Services\Messages\MessageGenerator;
use Valibool\TelegramConstruct\Services\Object\ChatMember;

class InputTGRequest
{
    private Bot $bot;
    private InputTextMessage|InputCallbackQuery|ChatMember|null $inputObject;
    private Request $inputRequest;
    private Message $answer;
    private Messages\Output\Output $outputMessage;


    public function __construct(Bot $bot, Request $request)
    {
        $this->inputRequest = $request;
        $this->bot = $bot;
    }


    public function start(): bool
    {
        $this->setInputObject();

        if($this->answer = $this->generateAnswer()){
            $this->sendAnswer();
        }
        return true;
    }

    public function sendAnswer(): void
    {
        $messageConstructor = new MessageConstructor($this->answer, $this->bot);
        $this->outputMessage = $messageConstructor->constructOutputMessage();
        $this->sendMessage();
    }


    public function sendMessage(): void
    {
        if($this->inputObject->lastMessage && $this->inputObject->lastMessage->buttons->count()){
            $this->outputMessage->lastTgMessageId = $this->inputObject->user->last_tg_message_id;
            $this->outputMessage->deletePrevMessage = true;
        }
        $result = $this->outputMessage->sendMessage($this->inputObject->chatId);


        if ($result->status){
            if ($result->message_id) {
                $this->saveLastMessage($result->message_id);
            }
            if($result->mediaGroupMessagesIds){
                $this->saveLastMessage($result->mediaGroupMessagesIds);
            }

            if ($this->answer->canSendNextMessage()) {
                $this->sendNextMessage();
            }
        }
    }
    public function sendNextMessage(): void
    {
        $this->answer = new MessageDB($this->answer->nextMessage);
        $this->sendAnswer();
    }

    public function saveLastMessage(int|array $tgMessageID): void
    {
        if($this->answer->messageId){
            $this->inputObject->user->saveLastMessage($this->answer->messageId, $tgMessageID);
        }
    }
    public function generateAnswer(): Message
    {
        $messageGenerator = new MessageGenerator($this->inputObject, $this->bot);
        return $messageGenerator->generateAnswer();
    }

    /**
     * @return void
     */
    public function setInputObject(): void
    {
        $this->inputObject = match (self::getObjectType($this->inputRequest)) {
            'message' => new InputTextMessage($this->inputRequest),
            'callback_query' => new InputCallbackQuery($this->inputRequest),
        };
    }

    /**
     * @param $input
     * @return string|null
     */
    public static function getObjectType($input): string|null
    {
        if ($input->message) {
            return 'message';
        }

        if ($input->callback_query) {
            return 'callback_query';
        }

        if ($input->chat_member) {
            return 'chat_member';
        }

        if ($input->my_chat_member) {
            return 'my_chat_member';
        }
        return null;
    }

}
