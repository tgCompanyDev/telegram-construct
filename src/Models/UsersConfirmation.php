<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;

class UsersConfirmation extends Model
{
    protected $fillable = [
        'input',
        'user_id'
    ];

}

