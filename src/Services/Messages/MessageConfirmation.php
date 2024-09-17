<?php

namespace Valibool\TelegramConstruct\Services\Messages;


use Illuminate\Database\Eloquent\Collection;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;
use Valibool\TelegramConstruct\Models\Keyboard;

class MessageConfirmation extends Message
{

    private string $inputText;
    public string $type = 'confirmation';

    public function __construct(string|null $text, string $inputText)
    {
        $this->inputText = $inputText;

        if (!$text) {
            $text = $this->defaultText();
        }
        $this->setText($text);
        $this->setType('confirmation');
        $this->setKeyboard($this->createKeyboard());

    }

    public function generateButtons(): Collection
    {
        return new Collection([
            [
                'text' => 'Подтверждаю',
                'callback_data' => 'confirmation_true'
            ]
        ]);
    }

    public function defaultText(): string
    {
        return 'Подтвердите ввод: ' . $this->inputText . '. Или введите заново';
    }

    public function createKeyboard()
    {
        $keyboard = new Keyboard();
        $keyboard->resize_keyboard = true;
        $keyboard->one_time_keyboard = true;
        $keyboard->buttons = $this->generateButtons();
        return $keyboard;
    }

    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function setKeyboard(Keyboard $keyboard): self
    {
        $this->keyboard = $keyboard;
        return $this;
    }

    public function setAttachments(Collection|TgConstructAttachment|null $attachments): self
    {
        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

}
