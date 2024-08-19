<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;

class MyChannel extends Model
{
    protected $fillable = [
        'channel_tg_id',
        'title',
        'username',
        'bot_id',
    ];

}

