<?php

namespace Valibool\TelegramConstruct\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TGAttachmentResource extends JsonResource
{

    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "name" => $this->original_name,
            "url" => $this->url,
            "mime" => $this->mime,
            "extension" => $this->extension,
            "size" => $this->size,
        ];
    }

}
