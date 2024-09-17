<?php

namespace Valibool\TelegramConstruct\Services\Messages;

use Illuminate\Database\Eloquent\Collection;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;
use Valibool\TelegramConstruct\Models\Keyboard;
use Valibool\TelegramConstruct\Models\Message as MessageDBModel;

class MessageDB extends Message
{

    public function __construct(MessageDBModel $messageDB)
    {
        $this->setText($messageDB->text);
        $this->setType($messageDB->type);
        $this->setKeyboard($messageDB->keyboard);
        $this->setAttachments($messageDB->attachment);

        $this->nextMessage = $messageDB->nextMessage;
        $this->messageId = $messageDB->id;
        $this->needConfirmation = $messageDB->need_confirmation;
        $this->needCacheMessage = true;
    }

    public function setNeedCache(): bool
    {
        return $this->needCacheMessage = true;
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
        $this->keyboard = $keyboard;
        return $this;
    }

    public function setAttachments(Collection|TgConstructAttachment|null $attachments): self
    {
        if($attachments->count()){
            $this->attachments = $attachments;
        }
        return $this;
    }

}
