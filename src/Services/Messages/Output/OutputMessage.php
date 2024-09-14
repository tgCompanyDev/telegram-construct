<?php

namespace Valibool\TelegramConstruct\Services\Messages\Output;

class OutputMessage extends Output
{
    public function __construct($token)
    {
        $this->token = $token;
        parent::__construct($token);
    }

    public function setText(string $text): string
    {
        return $this->text = $text;
    }

    public function setButtons(OutputButtons $buttons): self
    {
        $this->buttons = $buttons;
        return $this;
    }

    public function setPhoto($photo)
    {
        return $this->photo = $photo;
    }
    public function setMediaGroup(array $mediaGroup) : array
    {
        return $this->mediaGroup = $mediaGroup;
    }

    public function sendMessage(string $chatId) : self
    {
        if ($this->mediaGroup)
            return $this->sendMediaGroup($chatId);

        if ($this->photo)
            return $this->sendPhoto($chatId);

        return $this->sendTextMessage($chatId);

    }


}
