<?php

namespace Valibool\TelegramConstruct\Services\Messages;


use Illuminate\Database\Eloquent\Collection;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;
use Valibool\TelegramConstruct\Models\Keyboard;
use Valibool\TelegramConstruct\Models\Message as MessageDBModel;
use Valibool\TelegramConstruct\Services\Messages\Output\OutputButtons;

abstract class Message
{
    public string $text;
    public string $type;
    public bool $needCacheMessage = false;
    public Collection|TgConstructAttachment|null $attachments = null;
    protected Keyboard|null $keyboard = null;
    public int|null $messageId = null;
    public bool $needConfirmation = false;
    public MessageDBModel|null $nextMessage = null;

    abstract public function setText(string $text): self;
    abstract public function setType(string $type): self;
    abstract public function setKeyboard(Keyboard $keyboard): self;
    abstract public function setAttachments(Collection|TgConstructAttachment|null $attachments): self;

    public function canSendNextMessage() : bool
    {
        if($this->needConfirmation)
            return false;

        if($this->type === 'question')
            return false;

        if(!$this->nextMessage)
            return false;

        if($this->keyboard && $this->keyboard->buttons->count())
            return false;

        return true;
    }
    public function getButtons(): ?OutputButtons
    {
        $keyboard = null;
        if ($this->keyboard && $this->keyboard->buttons->count()) {
            $buttons = [];
            $countInRow = 1;
            $i = 0;

            $keyboard = new OutputButtons(
                $this->keyboard->resize_keyboard,
                $this->keyboard->one_time_keyboard
            );

            foreach ($this->keyboard->buttons as $button) {
                if (isset($button['callback_data'])) {
                    $buttons[] = ['text' => $button['text'], 'callback_data' => $button['callback_data']];
                }
            }
            $keyboard->row($buttons);
        }
        return $keyboard;
    }
}
