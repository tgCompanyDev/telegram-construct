<?php

namespace Valibool\TelegramConstruct\Services\Messages;


use Illuminate\Support\Collection;
use Valibool\TelegramConstruct\Models\Keyboard;
use Valibool\TelegramConstruct\Models\Message as MessageDBModel;

abstract class Message
{
    public string $text;
    public string $type;
    public Collection|null $buttons = null;
    public Collection|string|null $attachments;
    public Keyboard|null $keyboard = null;
    public int|null $messageId = null;
    public MessageDBModel|null $nextMessage = null;
    public bool $needConfirmation = false;




    public function __construct(string $text, $type = 'message', Collection|null $buttons = null, Collection|string|null $attachments = null)
    {
        $this->text = $text;
        $this->type = $type;
        $this->buttons = $buttons;
        $this->attachments = $attachments;
    }

    public function canSendNextMessage() : bool
    {
        if(($this->buttons && $this->buttons->count()) || !$this->nextMessage || $this->needConfirmation || $this->type === 'question'){
            return false;
        }
        return true;
    }
}
