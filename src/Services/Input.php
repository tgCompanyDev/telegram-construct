<?php

namespace Valibool\TelegramConstruct\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Update;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Services\Object\ChatMember;
use Valibool\TelegramConstruct\Services\Object\InputCallbackQuery;
use Valibool\TelegramConstruct\Services\Object\InputMessage;

class Input
{
    private Api $telegram;
    private Bot $bot;
    private InputMessage|InputCallbackQuery|ChatMember|null $inputObject;
    private Update $updates;


    /**
     * @throws TelegramSDKException
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
        $this->telegram = new Api($bot->token);
        $this->updates = $this->telegram->getWebhookUpdate();
    }

    /**
     * @return void
     * @throws TelegramSDKException
     */
    public function start(): void
    {
        $this->setInputObject();
    }

    /**
     * @return void
     * @throws TelegramSDKException
     */
    public function setInputObject(): void
    {
        switch ($this->updates->objectType()) {
            case 'callback_query':
                $this->inputObject = new InputCallbackQuery($this->telegram, $this->updates, $this->bot);
                if($this->inputObject->generateAnswer()){
                    $this->inputObject->sendAnswer();
                }
                break;
            case 'message':
                $this->inputObject = new InputMessage($this->telegram, $this->updates, $this->bot);
                if($this->inputObject->generateAnswer()) {
                    $this->inputObject->sendAnswer();
                }
                break;
            case 'my_chat_member':
                $this->inputObject = new ChatMember($this->telegram, $this->updates, $this->bot);
                $this->inputObject->checkChatMember();
                break;
            default:
                $this->inputObject = null;
        }
    }
}
