<?php

namespace Valibool\TelegramConstruct\Services;

use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard as SDKKeyboard;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Services\Messages\Message as MessageBase;
class MessageConstructService
{
    private Api $telegram;
    private string $chatId;
    public MessageBase $outputMessage;
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
     * @return void
     */
    private function setInlineKeyboardByMessage(): void
    {
        $outputMessageButtons = $this->outputMessage->buttons;

        if ($outputMessageButtons->count()) {
            $buttons = [];
            $countInRow = 1;
            $i=0;

            $keyboard = new SDKKeyboard([
                'resize_keyboard' => $outputMessageButtons->resize_keyboard ?? true,
                'one_time_keyboard' => $outputMessageButtons->one_time_keyboard ?? true
            ]);

            $keyboard->inline();

            foreach ($outputMessageButtons as $button) {
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
            $this->output->setKeyboard($keyboard);
        }
    }

    /**
     * Set the message text to the output object.
     *
     * @return void
     */
    private function setMessageText(): void
    {
        // Set the message text to the output object
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
        if ($image = $this->outputMessage->image) {
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


    public function setOutputMessage(MessageBase $message): Output
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
