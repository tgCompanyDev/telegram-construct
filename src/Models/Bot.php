<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    protected $fillable = [
        'name',
        'first_name',
        'user_name',
        'permissions',
        'description',
        'user_id',
        'token',
        'webhook',
        'secret_token',
    ];

    protected $casts = [
        'permissions' => 'array',
        'secret_token'=>'string',

    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function firstMessage()
    {
        return $this->hasOne(Message::class)->where('first_message', true);
    }
}

