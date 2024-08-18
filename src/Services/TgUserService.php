<?php

namespace Valibool\TelegramConstruct\Services;

use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Models\TgUser;

class TgUserService
{
    /**
     * @param $inputFrom
     * @return TgUser
     */
    public static function initUser($inputFrom) : TgUser
    {
        return TgUser::where('tg_user_id', $inputFrom['id'])->firstOrCreate([
            'tg_user_id' => $inputFrom['id'],
            'tg_user_name' => $inputFrom['username'] ?? null,
            'name' => $inputFrom['username'] ?? 'noname [' . $inputFrom['id'] . ']',
        ]);
    }

}
