<?php

namespace Valibool\TelegramConstruct\Observers;

use Valibool\TelegramConstruct\Models\Keyboard;
use Valibool\TelegramConstruct\Models\Message;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        Keyboard::create([
            'name' => $message->name,
            'message_id' => $message->id,
        ]);
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        if(($message->type != "modelMessage") && ($message->keyboard->model_class)){
            $message->keyboard->model_class = null;
            $message->keyboard->save();
        }
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     */
    public function restored(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     */
    public function forceDeleted(Message $message): void
    {
        //
    }
}