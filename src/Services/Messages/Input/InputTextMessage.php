<?php

namespace Valibool\TelegramConstruct\Services\Messages\Input;

use Illuminate\Http\Request;

class InputTextMessage extends InputObject
{
    public string $chatId;
    public string $type = 'message';


    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $inputObject = json_decode($request->getContent());
        $this->inputText = $inputObject->message->text;
        $this->setChatId($inputObject->message->chat->id);
        parent::__construct($inputObject->message->from);
    }

    public function setChatId(string $chatId): string
    {
       return $this->chatId = $chatId;
    }

}
