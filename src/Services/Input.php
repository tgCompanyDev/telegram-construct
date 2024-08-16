<?php

namespace Valibool\TelegramConstruct\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Services\Object\InputCallbackQuery;
use Valibool\TelegramConstruct\Services\Object\InputMessage;

class Input
{
    private Api $telegram;
    private Bot $bot;

    /**
     * @throws TelegramSDKException
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
        $this->telegram = new Api($bot->token);
        $this->updates = $this->telegram->getWebhookUpdate();
    }

    public function start()
    {
        $this->checkInputObject();
        $this->object->getAnswer();
    }

    public function checkInputObject()
    {
        switch ($this->updates->objectType()){
            case 'callback_query':
                $this->object = new InputCallbackQuery($this->telegram, $this->updates, $this->bot);
                break;
            case 'message':
                $this->object = new InputMessage($this->telegram, $this->updates, $this->bot);
                break;
        }
    }
}
