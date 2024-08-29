<?php

namespace Valibool\TelegramConstruct\Providers;

use Illuminate\Support\ServiceProvider;
use Valibool\TelegramConstruct\Console\Commands\InstallSwagger;

class TelegramConstructServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        $this->publishes([
            __DIR__ . '/../config/telegram-construct.php' => config_path('telegram-construct.php'),
        ], 'config');


        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

//        $this->commands([
////            InstallCommand::class,
//            InstallSwagger::class,
//        ]);

        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
    }
}
