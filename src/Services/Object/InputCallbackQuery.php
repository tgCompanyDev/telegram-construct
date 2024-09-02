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
    private TgUser $user;
    private MessageConstructService $messageService;
    private mixed $lastMessage;
    private string $lastTgMessageId;

    public function __construct($telegram, $inputCallbackQuery, Bot $bot)
    {
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->pressedButton = $inputCallbackQuery->callback_query['data'];
        $this->from = $inputCallbackQuery->callback_query['from'];
        $this->setUser();
    }

    /**
     * @return Output|null
     */
    public function generateAnswer(): ?Output
    {
       $outputMessage = $this->getOutputMessage();
        if($outputMessage){
            $this->lastTgMessageId = $this->user->last_tg_message_id;
            $this->messageService = new MessageConstructService($this->telegram, $this->from['id'], $this->bot,);
            $this->messageService->setLastTgMessageId($this->lastTgMessageId);
            return $this->messageService->setOutputMessage($outputMessage);
        }
        return null;
    }

    /**
     * @return bool
     */
    public function deletePrevMessage(): bool
    {
        return $this->messageService->deleteMessage();
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
        if ($message =  Message::find($this->pressedButton)){
            return $message;
        }
        return null;
    }

    /**
     * @return Message|null
     */
    private function getOutputMessage(): ?Message
    {
        if(!$this->checkSystemActions())
            return $this->getMessageByPressedButton();

        return  $this->user->lastQuestion->nextMessage;
    }

    /**
     * @return bool
     */
    public function checkSystemActions(): bool
    {
        if ($this->pressedButton == 'confirmation_true') {

            $validationService = new ValidationService();
            $validationService->confirmLastMessage($this->user);

            return true;
        }

        if ($this->pressedButton == 'skip') {

            $validationService = new ValidationService();
            $validationService->skipLastMessage($this->user);

            return true;
        }

        return false;
    }
}
