<?php

namespace Valibool\TelegramConstruct\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;


class InstallCommand extends Command
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'telegram-construct:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the telegram-construct files';

    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("Command executed with config param value " . Config::get('telegramConstruct.param'));
//        $this->comment('Installation started. Please wait...');

    }
}
