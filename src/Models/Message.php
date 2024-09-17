<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;
use Valibool\TelegramConstruct\Models\File\Traits\Attachable;

class Message extends Model
{
    use Attachable;

    protected $fillable = [
        'text',
        'bot_id',
        'name',
        'first_message',
        'type',
        'next_message_id',
        'need_confirmation',
        'wait_input',
        'keyboard_id',
    ];


    public static array $types = [
        'message' => 'Текстовое сообщение',
        'question' => 'Вопрос',
        'modelMessage' => 'Из модели',
    ];
    protected $with = ['keyboard'];

    /**
     * @return HasOne
     */
    public function nextMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'id', 'next_message_id');
    }

    /**
     * @return BelongsTo
     */
    public function prevMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'id', 'next_message_id');
    }

    /**
     * @return HasOne
     */
    public function keyboard(): HasOne
    {
        return $this->hasOne(Keyboard::class);
    }

    /**
     * @return HasManyThrough
     */
    public function buttons(): HasManyThrough
    {
        return $this->hasManyThrough(Button::class, Keyboard::class)->orderBy('id');
    }

}

