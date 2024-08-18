<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Attachment\Models\Attachment;

class Message extends Model
{

    protected $fillable = [
        'text',
        'bot_id',
        'name',
        'attachment_id',
        'first_message',
        'type',
        'next_message_id',
        'need_confirmation',
        'wait_input',
        'keyboard_id',
    ];

    protected $casts = [
        'wait_input' => 'array'
    ];

    public static array $types = [
        'message' => 'Текстовое сообщение',
        'question' => 'Вопрос',
        'modelMessage' => 'Из модели',
    ];
    protected $with = ['keyboard'];

    public function image()
    {
        return $this->hasOne(Attachment::class, 'id', 'attachment_id');
    }

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
        return $this->hasManyThrough(Button::class,Keyboard::class)->orderBy('id');
    }

    /**
     * @return bool
     */
    public function canSendNextMessage() : bool
    {
        if($this->buttons->count() || $this->keyboard->model_class || !$this->nextMessage || $this->need_confirmation){
            return false;
        }
        return true;

    }
}

