<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Services\API\MessageApiService;

class MessageController extends Controller
{
    public function __construct(protected MessageApiService  $messageApiService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->messageApiService->showAll();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'type' => 'required|string',
            'name' => 'required|string',
            'bot_id' => 'required|exists:bots,id',
        ]);
        return $this->messageApiService->store($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->messageApiService->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'type' => 'required|string',
            'name' => 'required|string',
            'next_message_id' => 'nullable|exists:messages,id',
        ]);
        return $this->messageApiService->update($id, $validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * @param int $message_id
     * @param Request $request
     * @return JsonResponse
     */
    public function syncButtons(int $message_id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'buttons'=>'array|nullable',
            'buttons.*.text'=>'required|string',
            'buttons.*.callback_data'=>'required|exists:messages,id',
        ]);

        return $this->messageApiService->syncButtons($message_id, $validated);
    }
    public function confirmSaveAll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bot_id' => 'required|exists:bots,id',
        ]);

        return $this->messageApiService->confirmSaveAll($validated['bot_id']);
    }
}
