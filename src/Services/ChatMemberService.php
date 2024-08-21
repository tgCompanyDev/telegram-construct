<?php

namespace Valibool\TelegramConstruct\Services;

use Valibool\TelegramConstruct\Models\MyChannel;

class ChatMemberService
{
    /**
     * @param $botId
     * @param $myChatMember
     * @return MyChannel
     */
    public static function standChannelAdmin($botId, $myChatMember): MyChannel
    {
        return MyChannel::create([
            'channel_tg_id' => $myChatMember['chat']['id'],
            'title' => $myChatMember['chat']['title'],
            'username' => $myChatMember['chat']['username'] ?? null,
            'bot_id' => $botId,
        ]);
    }

    /**
     * @param $botId
     * @param $myChatMember
     * @return false
     */
    public static function leftChannelAdmin($botId, $myChatMember): bool
    {
        $channel = MyChannel::where('channel_tg_id', $myChatMember['chat']['id'])
            ->where('bot_id', $botId)->first();
        if ($channel)
            return $channel->delete();
        return false;
    }
}
