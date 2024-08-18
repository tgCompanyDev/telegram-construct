<?php

namespace Valibool\TelegramConstruct\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Update;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Services\Object\InputCallbackQuery;
use Valibool\TelegramConstruct\Services\Object\InputMessage;

class Input
{
    private Api $telegram;
    private Bot $bot;
    private InputMessage|InputCallbackQuery|null $object;
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
        if ($this->object) {
            $this->object->generateAnswer();
            $this->object->sendAnswer();
        }
    }

    /**
     * @return void
     */
    public function setInputObject(): void
    {
        switch ($this->updates->objectType()) {
            case 'callback_query':
                $this->object = new InputCallbackQuery($this->telegram, $this->updates, $this->bot);
                break;
            case 'message':
                $this->object = new InputMessage($this->telegram, $this->updates, $this->bot);
                break;
            default:
                $this->object = null;
        }
    }
}
