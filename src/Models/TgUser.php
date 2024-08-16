<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;

class TgUser extends Model
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
    public function lastQuestion()
    {
        return $this->hasOne(Message::class,'id', 'last_message_id');
    }

    public function waitConfirmation()
    {
        return $this->hasOne(UsersConfirmation::class);
    }

    public function mustAnswer():bool
    {
        if ($this->waitConfirmation || ($this->lastQuestion &&  $this->lastQuestion->type =='question' )) {
            return true;
        }
        return false;
    }

}

