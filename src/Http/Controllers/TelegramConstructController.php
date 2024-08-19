<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Services\Input;


class TelegramConstructController extends Controller
{

    public function input(Request $request)
    {

        Log::debug(json_encode($request->all(), JSON_PRETTY_PRINT));
        if ($request->header('X-Telegram-Bot-Api-Secret-Token')) {
            if ($bot = Bot::where('secret_token', $request->header('X-Telegram-Bot-Api-Secret-Token'))->first()) {
                $input = new Input($bot);
                $input->start();
            }
        }
        return 'ok';
    }

    public function test(){
        return "здорова ебать";
    }
}
