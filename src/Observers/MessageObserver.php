<?php

namespace Valibool\TelegramConstruct\Observers;

use Illuminate\Support\Facades\Cache;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Models\Button;
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
       Cache::tags(['answers'])->flush();

//        if(($message->type != "modelMessage") && ($message->keyboard->model_class)){
//            $message->keyboard->model_class = null;
//            $message->keyboard->save();
//        }
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        $buttonsOnDeletedMessages = Button::where('callback_data', $message->id)->get();
        foreach ($buttonsOnDeletedMessages as $button){
            $button->callback_data = null;
            $button->save();
        }
        Cache::tags(['answers'])->flush();

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
