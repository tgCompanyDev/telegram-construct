<?php

namespace Valibool\TelegramConstruct\Services\Messages\Input;

use Illuminate\Http\Request;
use Valibool\TelegramConstruct\Models\Message;

class InputCallbackQuery extends InputObject
{
    public string $type = 'callback_query';
    public string $chatId;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $inputObject = json_decode($request->getContent());
        $this->setChatId($inputObject->callback_query->message->chat->id);
        $this->callBackData = $inputObject->callback_query->data;
        parent::__construct($inputObject->callback_query->from);

        $this->checkCallBackData();

    }

    public function checkCallBackData(): void
    {

        match ($this->callBackData) {
            'confirmation_true' => $this->confirmLastInput(),
            $this->messageFromDB() => false,
            $this->anyCallbackData() => $this->parseAnyCallbackData(),
        };

    }

    public function anyCallbackData()
    {
       return $this->callBackData;
    }

    public function parseAnyCallbackData()
    {
        return null;
    }

    public function messageFromDB()
    {
        if ($message = Message::find($this->callBackData)) {
            $this->nextMessage = $message;
            return (string)$message->id;
        }
    }

    public function confirmLastInput(): void
    {
        $this->type = 'confirmation';
        $this->user->confirmLastInput();
    }

    public function setChatId(string $chatId): string
    {
        return $this->chatId = $chatId;
    }

}
