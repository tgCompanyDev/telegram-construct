<?php

namespace Valibool\TelegramConstruct\Services\Object;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\MyChannel;
use Valibool\TelegramConstruct\Services\ChatMemberService;
use Valibool\TelegramConstruct\Services\TgUserService;

class ChatMember
{
//    private ChatMemberService $chatMemberService;
    private Api $telegram;
    private Bot $bot;
    private mixed $myChatMember;
    private mixed $from;

    public function __construct(Api $telegram, Update $myChatMember, Bot $bot)
    {
//        $this->chatMemberService = new ChatMemberService();
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->myChatMember = $myChatMember->my_chat_member;
        $this->from = $myChatMember->my_chat_member['from'];
        $this->setUser();
    }

    /**
     * @return void
     */
    public function setUser(): void
    {
        $this->user = TgUserService::initUser($this->from);
    }

    /**
     * @return void
     */
    public function checkChatMember(): void
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

    /**
     * @return MyChannel
     */
    private function botStandChannelAdmin(): MyChannel
    {
        return $this->bot->standChannelAdmin($this->myChatMember);

    }

    /**
     * @return bool
     */
    private function botLeftChannelAdmin(): bool
    {
        return $this->bot->leftChannelAdmin($this->myChatMember);
    }
}
