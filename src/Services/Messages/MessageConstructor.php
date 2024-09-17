<?php

namespace Valibool\TelegramConstruct\Services\Messages;

use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message as MessageModel;
use Valibool\TelegramConstruct\Services\Messages\Output\OutputButtons;

class MessageConstructor
{

    public ?OutputButtons $buttons;

    public function __construct(Message $message)
    {
        $this->text = $message->text;
        $this->type = $message->type;
        $this->buttons = $message->getButtons();
        $this->attachments = $message->attachments ? $message->attachments->select(['id','name','disk','path','extension','mime']) : null;
    }

    /**
     * @param Bot $bot
     * @return MessageModel
     */
    public static function createStartDefaultMessage(Bot $bot) : MessageModel
    {
        return MessageModel::create([
            'bot_id' => $bot->id,
            'name' => 'Стартовое сообщение',
            'text' => 'Добро пожаловать',
            'first_message' => true,
        ]);
    }
}
