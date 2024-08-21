<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Valibool\TelegramConstruct\Services\API\PostApiService;

class PostController extends Controller
{
    public function __construct(protected PostApiService $postApiService)
    {
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request): mixed
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'text' => 'required|string',
            'type' => 'required|string',
            'bot_id' => 'required|exists:bots,id',
            'attachment_id' => 'nullable|exists:tg_construct_attachments,id',

        ]);
        return $this->postApiService->store($validated);
    }

    /**
     * @param int $post
     * @return mixed
     */
    public function show(int $post): mixed
    {
        return $this->postApiService->show($post);
    }

    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return $this->postApiService->showAll();
    }

    /**
     * @param Request $request
     * @param int $post
     * @return mixed
     */
    public function update(Request $request, int $post): mixed
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'text' => 'required|string',
            'type' => 'required|string',
            'attachment_id' => 'nullable|exists:tg_construct_attachments,id',

        ]);
        return $this->postApiService->update($post, $validated);
    }

    /**
     * @param int $post
     * @return mixed
     */
    public function destroy(int $post): mixed
    {
        return $this->postApiService->destroy($post);
    }

    public function getMessageViews(int $post_id)
    {
        return $this->telegramClient->getMessageViews($post_id);
    }
}
