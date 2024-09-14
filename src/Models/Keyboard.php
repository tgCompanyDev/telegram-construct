<?php

namespace Valibool\TelegramConstruct\Models;

use Illuminate\Database\Eloquent\Model;
use Valibool\TelegramConstruct\Models\Relation\SyncableModel;

class Keyboard extends SyncableModel
{

    protected $fillable = [
        'name',
        'message_id',
        'resize_keyboard',
        'one_time_keyboard',
        'model_class',
    ];
    protected $with = ['buttons'];

    public function buttons()
    {
        return $this->hasMany(Button::class)->orderBy('id');
    }
    public function messages()
    {
        return $this->belongsToMany(Message::class);
    }
    public function isDynamic() : bool
    {
        return (bool) $this->model_class;
    }

    public function connectedModel()
    {
        return app($this->model_class);
    }
}

