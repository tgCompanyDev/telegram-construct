<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;

class UserTgSettings extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'name',
        'last_message_id',
        'last_tg_message_id',
        'phone',
        'tg_user_id',
        'tg_user_name',
    ];

}

