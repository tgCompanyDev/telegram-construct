<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function nextMessage()
    {
        return $this->hasOne(Message::class, 'id', 'next_message_id');
    }
    public function prevMessage()
    {
        return $this->belongsTo(Message::class, 'id', 'next_message_id');
    }

    public function keyboard()
    {
        return $this->hasOne(Keyboard::class);
    }
    public function buttons()
    {
        return $this->hasManyThrough(Button::class,Keyboard::class)->orderBy('id');
    }
    public function canSendNextMessage() : bool
    {
        if($this->buttons->count() || $this->keyboard->model_class || !$this->nextMessage || $this->need_confirmation){
            return false;
        }
        return true;

    }
}

