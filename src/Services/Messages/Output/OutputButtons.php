<?php

namespace Valibool\TelegramConstruct\Services\Messages\Output;

class OutputButtons
{
    private bool $resizeKeyboard;
    private bool $oneTimeKeyboard;
    public string|false $keyboard;
    private array $row = [];


    /**
     * @param bool $resizeKeyboard
     * @param bool $oneTimeKeyboard
     */
    public function __construct(bool $resizeKeyboard, bool $oneTimeKeyboard)
    {
        $this->resizeKeyboard = $resizeKeyboard;
        $this->oneTimeKeyboard = $oneTimeKeyboard;
    }

    /**
     * @param $buttons
     * @return $this
     */
    public function row($buttons): self
    {
        $this->row[] = $buttons;
        $this->generateKeyboard();

        return $this;
    }

    /**
     * @return void
     */
    public function generateKeyboard(): void
    {
        $this->keyboard = json_encode([
            'resizeKeyboard' => $this->resizeKeyboard,
            'oneTimeKeyboard' => $this->oneTimeKeyboard,
            'inline_keyboard' => $this->row,
        ]);
    }
}
