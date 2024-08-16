<?php

namespace Valibool\TelegramConstruct\Providers;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Observers\MessageObserver;

class TelegramConstructEventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Message::observe(MessageObserver::class);
    }
}