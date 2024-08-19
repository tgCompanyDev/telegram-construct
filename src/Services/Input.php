<?php

namespace Valibool\TelegramConstruct\Services;

use Illuminate\Support\Facades\Log;
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
    private InputMessage|InputCallbackQuery|ChatMember|null $object;
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
     */
    public function setInputObject(): void
    {

        Log::debug($this->updates->objectType());
        switch ($this->updates->objectType()) {
            case 'callback_query':
                $this->object = new InputCallbackQuery($this->telegram, $this->updates, $this->bot);
                $this->object->generateAnswer();
                $this->object->sendAnswer();
                break;
            case 'message':
                $this->object = new InputMessage($this->telegram, $this->updates, $this->bot);
                $this->object->generateAnswer();
                $this->object->sendAnswer();
                break;
            case 'my_chat_member':
                $this->object = new ChatMember($this->telegram, $this->updates, $this->bot);
                $this->object->checkChatMember();
                break;
            default:
                $this->object = null;
        }
    }
}
