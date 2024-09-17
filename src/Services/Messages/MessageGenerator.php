<?php

namespace Valibool\TelegramConstruct\Services\Messages;

use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Services\Messages\Input\InputObject;
use Valibool\TelegramConstruct\Services\Messages\Message as MessageBase;

class MessageGenerator
{
    private Bot $bot;
    private InputObject $inputObject;

    /**
     * @param InputObject $inputObject
     * @param Bot $bot
     */
    public function __construct(InputObject $inputObject, Bot $bot)
    {
        $this->inputObject = $inputObject;
        $this->bot = $bot;
    }

    /**
     * @return Message|null
     */
    private function getFirstMessage(): Message|null
    {
        return Message::where('first_message', true)->where('bot_id', $this->bot->id)->first();
    }

    /**
     * @return \Valibool\TelegramConstruct\Services\Messages\Message
     */
    public function generateAnswer(): MessageBase
    {
        if ($validationMessage = $this->checkValidation())
            return $validationMessage;

        if ($answer = $this->inputObject->getAnswer())
            return $answer;

        return new MessageDB($this->getFirstMessage());
    }

    /**
     * @return MessageValidation|MessageConfirmation|null
     */
    protected function checkValidation(): MessageValidation|MessageConfirmation|null
    {

        if($this->inputObject->type === 'confirmation'){
            return null;
        }

        if($this->inputObject->user->mustAnswer()){
            if ($this->inputObject->getValidateFail()) {
                return $this->inputObject->getValidationErrorMessage();
            }
            return $this->inputObject->createValidationConfirmationMessage();
        }
       return null;
    }
}
