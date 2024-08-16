<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    public function userTgSettings()
    {
        return $this->hasMany('Valibool\TelegramConstruct\Models\UserTgSettings', 'user_id', 'id');
    }
}

