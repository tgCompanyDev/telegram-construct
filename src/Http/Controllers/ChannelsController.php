<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use Illuminate\Http\Request;
use Valibool\TelegramConstruct\Services\API\ChannelsApiService;

class ChannelsController extends Controller
{
    public function __construct(protected ChannelsApiService $channelsApiService)
    {
    }

    public function getBotsChannels(Request $request)
    {
        $validated = $request->validate([
            'bot_id' => 'required|exists:bots,id',
        ]);

       return $this->channelsApiService->getBotsChannels($validated['bot_id']);
    }

}
