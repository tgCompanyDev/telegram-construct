<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;

class Post extends Model
{

    protected $fillable = [
        'text',
        'type',
        'name',
        'bot_id',
        'attachment_id',
    ];

    public function image()
    {
        return $this->hasOne(TgConstructAttachment::class, 'id', 'attachment_id');
    }

}

