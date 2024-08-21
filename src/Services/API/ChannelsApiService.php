<?php

namespace Valibool\TelegramConstruct\Services\API;

use Valibool\TelegramConstruct\Http\Resources\ChannelResource;
use Valibool\TelegramConstruct\Models\Bot;
use Valibool\TelegramConstruct\Services\Response\ResponseService;

class ChannelsApiService
{

    public function getBotsChannels($botId)
    {
        if($bot = Bot::with('channels')->find($botId)){
            return ResponseService::success(ChannelResource::collection($bot->channels));
        }
        return ResponseService::notFound();
    }
}
