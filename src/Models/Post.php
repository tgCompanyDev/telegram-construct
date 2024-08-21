<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Models\Attachment;

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
        return $this->hasOne(Attachment::class, 'id', 'attachment_id');
    }

}

