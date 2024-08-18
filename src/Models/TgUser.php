<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    /**
     * @return HasOne
     */
    public function lastQuestion(): HasOne
    {
        return $this->hasOne(Message::class, 'id', 'last_message_id');
    }

    /**
     * @return HasOne
     */
    public function waitConfirmation(): HasOne
    {
        return $this->hasOne(UsersConfirmation::class);
    }

    /**
     * @return bool
     */
    public function mustAnswer(): bool
    {
        if ($this->waitConfirmation || ($this->lastQuestion && $this->lastQuestion->type == 'question')) {
            return true;
        }
        return false;
    }

    /**
     * @param Message $message
     * @param string $lastTgMessageId
     * @return bool
     */
    public function saveLastMessage(Message $message, string $lastTgMessageId): bool
    {
        $this->last_message_id = $message->id;
        $this->last_tg_message_id = $lastTgMessageId;
        return $this->save();
    }

}

