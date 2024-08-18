<?php

namespace Valibool\TelegramConstruct\Services;

use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Models\User;

class TelegramService
{

    /**
     * @param Bot $bot
     * @return Message
     */
    public static function createStartDefaultMessage(Bot $bot) : Message
    {
       return Message::create([
            'bot_id' => $bot->id,
            'name' => 'Стартовое сообщение',
            'text' => 'Добро пожаловать',
            'first_message' => true,
        ]);
    }

    public static function getUserInputValuesType()
    {
        return [
            User::class => [
                'name' => 'Пользователь',
                'values' => User::fieldsToUserInput(),
            ],
        ];
    }

    public static function ModelsForDialogButtons()
    {
        return [
//            City::class => [
//                'keyToNameButton' => 'name'
//            ],
//            Restaurant::class => [
//                'keyToNameButton' => 'name'
//            ]
        ];
    }
}
