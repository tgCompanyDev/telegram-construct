<?php

namespace Valibool\TelegramConstruct\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{

    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "text" => $this->text,
            "type" => $this->type,
            "first_message" => $this->first_message,
            "wait_input" => $this->wait_input,
            "need_confirmation" => $this->need_confirmation,
            "next_message_id" => $this->next_message_id,
            "attachments" => $this->attachment? TGAttachmentResource::collection($this->attachment): null,
            "buttons" => MessageButtonsResource::collection($this->buttons),
        ];
    }

}
