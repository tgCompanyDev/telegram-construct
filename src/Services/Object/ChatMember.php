<?php

namespace Valibool\TelegramConstruct\Services\Object;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Services\ChannelsService;
use Valibool\TelegramConstruct\Services\TgUserService;

class ChatMember
{
    private ChannelsService $channelService;
    private Api $telegram;
    private Bot $bot;
    private mixed $myChatMember;
    private mixed $from;

    public function __construct(Api $telegram, Update $myChatMember, Bot $bot)
    {
        $this->channelService = new ChannelsService();
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->myChatMember = $myChatMember->my_chat_member;
        $this->from = $myChatMember->my_chat_member['from'];
        $this->setUser();
    }

    public function setUser(): void
    {
        $this->user = TgUserService::initUser($this->from);
    }

    public function checkChatMember()
    {
        if(
            $this->myChatMember['old_chat_member']['status'] == 'left' &&
            $this->myChatMember['new_chat_member']['status'] == 'administrator'
        ) {
            $this->botStandChannelAdmin();
        }

        if(
            $this->myChatMember['new_chat_member']['status'] == 'left' &&
            $this->myChatMember['old_chat_member']['status'] == 'administrator'
        ) {
            $this->botLeftChannelAdmin();
        }
    }
    private function botStandChannelAdmin()
    {
        $channel = $this->bot->standChannelAdmin($this->myChatMember);

    }
    private function botLeftChannelAdmin()
    {
        $deleted = $this->bot->leftChannelAdmin($this->myChatMember);

    }
}
