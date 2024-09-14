<?php

namespace Valibool\TelegramConstruct\Services\Messages;

use Valibool\TelegramConstruct\Models\Message as MessageDBModel;

class MessageDB extends Message
{

    public function __construct(MessageDBModel $messageDB)
    {
        parent::__construct($messageDB->text, $messageDB->type, $messageDB->buttons, $messageDB->attachment);
        $this->keyboard = $messageDB->keyboard;
        $this->needConfirmation = $messageDB->need_confirmation;
        $this->nextMessage = $messageDB->nextMessage;
        $this->messageId = $messageDB->id;
    }



}
