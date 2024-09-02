<?php

namespace Valibool\TelegramConstruct\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard as SDKKeyboard;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message;

class MessageConstructService
{
    private Api $telegram;
    private string $chatId;
    public Message $outputMessage;
    private Bot $bot;
    private string $lastTgMessageId;

    public function __construct(
        Api    $telegram,
        string $chatId,
        Bot    $bot,
    )
    {
        $this->telegram = $telegram;
        $this->bot = $bot;
        $this->chatId = $chatId;
        $this->output = new Output();
    }

    /**
     * @return void
     */
    private function setClient(): void
    {
        $this->output->setClient($this->telegram);
    }

    /**
     * @param $message
     * @return void
     */
    private function setInlineKeyboardByMessage(): void
    {
        if ($this->outputMessage->buttons) {
            $buttons = [];
            $countInRow = 1;
            $i=0;

            $keyboard = new SDKKeyboard([
                'resize_keyboard' => $this->outputMessage->keyboard->resize_keyboard ?? true,
                'one_time_keyboard' => $this->outputMessage->keyboard->one_time_keyboard ?? true
            ]);

            $keyboard->inline();

            foreach ($this->outputMessage->buttons as $button) {
                if (isset($button['callback_data'])) {
                    $buttons[] = SDKKeyboard::inlineButton(['text' => $button['text'], 'callback_data' => $button['callback_data']]);
                }
                if($i == $countInRow-1){
                    $keyboard->row($buttons);
                    $buttons=[];
                    $i=0;
                } else {
                    $i++;
                }
            }
//            $keyboard->row($buttons);
            $this->output->setKeyboard($keyboard);
        }
    }
    private function setReplyKeyboardByMessage(): void
    {
        $countInRow = 3;
        $rows = [];
        $buttons = [];
        $i=1;
        foreach ($this->outputMessage->buttons as $button) {

            $buttons[] = SDKKeyboard::button($button['text']);

            if($i==$countInRow){
                $i=0;
            } else {
                $rows[] = $buttons;
                $i++;
            }
        }
        dd($rows);

//        $keyboard = SDKKeyboard::make()
//            ->setResizeKeyboard(true)
//            ->setOneTimeKeyboard(true)
//            ->row([
//                SDKKeyboard::button('1'),
//                SDKKeyboard::button('2'),
//                SDKKeyboard::button('3'),
//                SDKKeyboard::button('4'),
//                SDKKeyboard::button('5'),
//                SDKKeyboard::button('6'),
//                SDKKeyboard::button('7'),
//                SDKKeyboard::button('8'),
//                SDKKeyboard::button('9'),
//            ]);
//            ->row([
//                SDKKeyboard::button('4'),
//                SDKKeyboard::button('5'),
//                SDKKeyboard::button('6'),
//            ])
//            ->row([
//                SDKKeyboard::button('7'),
//                SDKKeyboard::button('8'),
//                SDKKeyboard::button('9'),
//            ])
//            ->row([
//                SDKKeyboard::button('0'),
//            ]);
//        if ($this->outputMessage->buttons) {
//            $buttons = [];
//
//            $keyboard = new SDKKeyboard([
//                'resize_keyboard' => $this->outputMessage->keyboard->resize_keyboard,
//                'one_time_keyboard' => $this->outputMessage->keyboard->one_time_keyboard
//            ]);
//
//            dd
//            $keyboard->inline();
//
//            foreach ($this->outputMessage->buttons as $button) {
//                if (isset($button['callback_data'])) {
//                    $buttons[] = SDKKeyboard::inlineButton(['text' => $button['text'], 'callback_data' => $button['callback_data']]);
//                }
//            }
//            $keyboard->row($buttons);
//
            $this->output->setKeyboard($keyboard);
//        }
    }

    /**
     * @param $message
     * @return void
     */
    private function setMessageText(): void
    {
        $this->output->setMessageText($this->outputMessage->text);
    }

    /**
     * @return void
     */
    private function setChatId(): void
    {
        $this->output->setChatId($this->chatId);
    }

    /**
     * @return void
     */
    private function setPhoto(): void
    {
        if ($this->outputMessage->image) {
            $this->output->setPhoto($this->outputMessage->image);
        }
    }

    /**
     * @return Output
     */
    public function constructOutputMessage(): Output
    {
        $this->setClient();
        $this->setInlineKeyboardByMessage();
        $this->setMessageText();
        $this->setChatId();
        $this->setPhoto();

        return $this->output;
    }

    /**
     * @throws TelegramSDKException
     */
    public function sendMessage(): \Telegram\Bot\Objects\Message
    {
        return $this->output->sendMessage();
    }

    /**
     * @param Message $message
     * @return Output
     */
    public function setOutputMessage(Message $message): Output
    {
        $this->outputMessage = $message;
        $this->constructOutputMessage();

        return $this->output;
    }

    /**
     * @param string $messageTgId
     * @return string
     */
    public function setLastTgMessageId(string $messageTgId): string
    {
        return $this->lastTgMessageId = $messageTgId;
    }

    /**
     * @return bool
     */
    public function deleteMessage(): bool
    {
       return $this->output->deleteMessage($this->lastTgMessageId);
    }

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
}
