<?php

namespace Valibool\TelegramConstruct\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageButtonsResource extends JsonResource
{

    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "text" => $this->text,
            "callback_data" => $this->callback_data,
        ];
    }

}
