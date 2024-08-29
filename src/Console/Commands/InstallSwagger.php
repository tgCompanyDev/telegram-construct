<?php

namespace Valibool\TelegramConstruct\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;


class InstallSwagger extends Command
{

    protected $name = 'telegram-construct:swagger-publish';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'telegram-construct:swagger-publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install swagger';

//    public function __construct()
//    {
//        parent::__construct();
//    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        dd('ss00');

//        $this->info("Command executed with config param value " . Config::get('telegramConstruct.param'));
//        $this->comment('Installation started. Please wait...');
        Artisan::call('vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"');
    }
}
