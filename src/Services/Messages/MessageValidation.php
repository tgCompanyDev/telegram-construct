<?php

namespace Valibool\TelegramConstruct\Services\Messages;


class MessageValidation extends Message
{

    public function __construct($text)
    {
        $type = 'validation_error';
        parent::__construct($text, $type);
    }
}
