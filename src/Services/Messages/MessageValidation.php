<?php

namespace Valibool\TelegramConstruct\Services\Messages;


use Illuminate\Database\Eloquent\Collection;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;
use Valibool\TelegramConstruct\Models\Keyboard;

class MessageValidation extends Message
{

    public function __construct($text)
    {
        $this->setText($text);
        $this->setType('validation_error');
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
    public function setKeyboard(Keyboard $keyboard): self
    {
        return $this;
    }
    public function setAttachments(Collection|TgConstructAttachment|null $attachments): self
    {
        return $this;
    }
}
