<?php

namespace Valibool\TelegramConstruct\Services\Messages;


use Illuminate\Support\Collection;
use Valibool\TelegramConstruct\Models\Keyboard;

class MessageConfirmation extends Message
{

    private string $inputText;
    public string $type = 'confirmation';

    public function __construct(string|null $messageText, string $inputText)
    {
        $this->inputText = $inputText;

        $this->setText($messageText);
        $this->setKeyboard();
        $buttons = $this->getButtons();

        parent::__construct($this->text, $this->type, $buttons);

    }
    public function setText($messageText): void
    {
        if ($messageText) {
            $this->text = $messageText;
        } else {
            $this->defaultText();
        }
    }

    public function setKeyboard(): void
    {
        $this->keyboard = new Keyboard();
        $this->keyboard->resize_keyboard = true;
        $this->keyboard->one_time_keyboard = true;
    }

    public function getButtons(): Collection
    {
        return new Collection([
            [
                'text' => 'Подтверждаю',
                'callback_data' => 'confirmation_true'
            ]
        ]);
    }

    public function defaultText(): void
    {
        $this->text = 'Подтвердите ввод: ' . $this->inputText . '. Или введите заново';

    }
}
