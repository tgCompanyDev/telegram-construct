<?php

namespace Valibool\TelegramConstruct\Services\Object;

use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard as SDKKeyboard;
use Telegram\Bot\Objects\Update;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Models\TgUser;
use Valibool\TelegramConstruct\Services\MessageConstructService;
use Valibool\TelegramConstruct\Services\Messages\MessageConfirmation;
use Valibool\TelegramConstruct\Services\Messages\MessageDB;
use Valibool\TelegramConstruct\Services\Messages\MessageValidation;
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
    public MessageConstructService $messageService;

    public function __construct(Api $telegram, Update $inputMessage, Bot $bot)
    {
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->firstMessage = $this->getFirstMessage();
        $this->from = $inputMessage->message['from'];
        $this->inputMessage = $inputMessage;
        $this->setUser();
    }

    /**
     * @return Output
     */
    public function generateAnswer(): Output
    {
        $this->messageService = new MessageConstructService($this->telegram, $this->from['id'], $this->bot);

        if ($this->user->mustAnswer()) {
            $outputMessage = $this->validateErrorsInput();

            return $this->messageService->setOutputMessage($outputMessage);
        }
        if ($this->user->lastQuestion) {
            $this->lastMessageId = $this->user->lastQuestion->id;
            $outputMessage = $this->createOutputMessage($this->user->lastQuestion);
        } else {
            $this->lastMessageId = $this->firstMessage->id;

            $outputMessage = $this->createOutputMessage($this->firstMessage);

        }
        return $this->messageService->setOutputMessage($outputMessage);
    }

    /**
     * @param Message $message
     * @return MessageDB
     */
    public function createOutputMessage(Message $message) : MessageDB
    {
        return new MessageDB($message);
    }

    /**
     * @return \Telegram\Bot\Objects\Message
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function sendAnswer(): \Telegram\Bot\Objects\Message
    {
        $answer = $this->messageService->sendMessage();
        $outputMessageType = $this->messageService->outputMessage->type;

        if (!in_array($outputMessageType, ['confirmation','validation_error'])) {
            $this->user->saveLastMessage($this->lastMessageId, $answer->message_id);
            if ($this->messageService->outputMessage->canSendNextMessage()) {
                if ($nextMessageSent = $this->sendNextMessage()) {
                    $answer = $nextMessageSent;
                }
            }
        } else {
            $this->user->last_tg_message_id = $answer->message_id;
            $this->user->save();
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
        if ($this->user->last_message_id) {
            if ($nextMessage = $this->user->lastQuestion->nextMessage) {
                $outputMessage = $this->createOutputMessage($nextMessage);
                $this->messageService->setOutputMessage($outputMessage);
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
        return Message::where('first_message', true)->where('bot_id', $this->bot->id)->first();
    }

    /**
     * @return MessageValidation|MessageConfirmation
     */
    private function validateErrorsInput(): MessageValidation|MessageConfirmation
    {
        $validationService = new ValidationService();

        $validatedInputFail = $validationService->validateAnswerTheQuestion(
            $this->inputMessage->getMessage()->text,
            $this->user->lastQuestion
        );

        if ($validatedInputFail)
            return $validationService->getValidationErrorMessage();

        return $validationService->getConfirmationMessage(
            $this->inputMessage->getMessage()->text,
            $this->user
        );
    }
}
