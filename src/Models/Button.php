<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;

class Button extends Model
{
    protected $fillable = [
        'keyboard_id',
        'text',
        'callback_data',
    ];

    public function keyboard()
    {
        return $this->belongsTo(Keyboard::class);
    }
}

