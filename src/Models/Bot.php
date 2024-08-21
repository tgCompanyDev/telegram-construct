<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;
use Valibool\TelegramConstruct\Services\ChatMemberService;

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

    public function channels()
    {
        return $this->hasMany(MyChannel::class);
    }
    public function firstMessage()
    {
        return $this->hasOne(Message::class)->where('first_message', true);
    }

    public function standChannelAdmin($myChatMember)
    {
       return ChatMemberService::standChannelAdmin($this->id, $myChatMember);
    }
    public function leftChannelAdmin($myChatMember)
    {
       return ChatMemberService::leftChannelAdmin($this->id, $myChatMember);
    }
}

