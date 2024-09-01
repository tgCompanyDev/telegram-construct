<?php

namespace Valibool\TelegramConstruct\Services;

use Telegram\Bot\Api;
use Valibool\TelegramConstruct\Models\Bot;

class TelegramCommands
{
    public static function setWebhook(Api $telegram, string $url, Bot $bot)
    {
        $params = [
            'allowed_updates' => [
                'message',
                'message_reaction',
                'message_reaction_count',
                'edited_message',
                'channel_post',
                'edited_channel_post',
                'inline_query',
                'chosen_inline_result',
                'callback_query',
                'shipping_query',
                'pre_checkout_query',
                'poll',
                'poll_answer',
                'my_chat_member',
                'chat_member',
                'chat_join_request'
            ],
            'url' => $url,
            'secret_token' => $bot->secret_token,
        ];
        $webhook = $telegram->setWebhook($params);
        if ($webhook) {
            $bot->webhook = $url;
            $bot->save();
        }
        return $webhook;
    }

    public static function getWebhookInfo(Api $telegram){
        return $telegram->getWebhookInfo();
    }
}
