<?php

namespace Valibool\TelegramConstruct\Providers;

use Illuminate\Support\ServiceProvider;
use Valibool\TelegramConstruct\Commands\InstallCommand;

class TelegramConstructServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/../../config/telegramConstruct.php' => config_path('telegramConstruct.php'),
        ], 'public');

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

//        $this->commands([
//            InstallCommand::class,
//        ]);

        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    }
}
