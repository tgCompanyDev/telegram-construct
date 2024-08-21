<?php

namespace Valibool\TelegramConstruct\Observers;

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
        $bot = Bot::find($message->bot_id);
        foreach ($bot->messages as $message) {

        }
        dd($bot->messages);
//        $buttons = Button::where()
//        dd($message);
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
