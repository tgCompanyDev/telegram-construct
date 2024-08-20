<?php

namespace Valibool\TelegramConstruct\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BotResource extends JsonResource
{

    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "first_name" => $this->first_name,
            "user_name" => $this->user_name,
            "webhook" => $this->webhook,
            "permissions" => $this->permissions,
        ];
    }

}
