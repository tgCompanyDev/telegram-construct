<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use Illuminate\Http\Request;
use Valibool\TelegramConstruct\Services\TGServerClient\TGServerClient;

class TelegramServerClientController extends Controller
{
    public function __construct(protected TGServerClient $telegramClient)
    {
    }

    public function auth()
    {
        return $this->telegramClient->auth();
    }

    public function logout(Request $request)
    {
        $this->telegramClient->logout();
    }
    public function getMessageViews(int $post_id)
    {
        return $this->telegramClient->getMessageViews($post_id);
    }
}
