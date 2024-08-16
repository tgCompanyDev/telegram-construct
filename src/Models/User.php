<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'permissions',
    ];

    protected $casts = [
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    public function userTgSettings()
    {
        return $this->hasMany('Valibool\TelegramConstruct\Models\UserTgSettings', 'user_id', 'id');
    }
}

