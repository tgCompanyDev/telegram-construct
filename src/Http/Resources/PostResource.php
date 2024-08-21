<?php

namespace Valibool\TelegramConstruct\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{

    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "text" => $this->text,
            "type" => $this->type,
            "image" => $this->image? $this->image->url : null,
        ];
    }

}
