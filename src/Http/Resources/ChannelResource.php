<?php

namespace Valibool\TelegramConstruct\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "channel_tg_id" => $this->channel_tg_id,
            "username" => $this->username,
        ];
    }

}
