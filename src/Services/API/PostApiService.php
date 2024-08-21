<?php

namespace Valibool\TelegramConstruct\Services\API;

use Illuminate\Http\JsonResponse;
use Valibool\TelegramConstruct\Http\Resources\PostResource;
use Valibool\TelegramConstruct\Models\Post;
use Valibool\TelegramConstruct\Services\Response\ResponseService;

class PostApiService
{
    /**
     * @return JsonResponse
     */
    public function showAll(): JsonResponse
    {
        $posts = Post::all();
        if ($posts) {
            return ResponseService::success(
                PostResource::collection($posts)
            );
        }
        return ResponseService::notFound();
    }

    /**
     * @param int $postId
     * @return JsonResponse
     */
    public function show(int $postId): JsonResponse
    {
        $post = Post::find($postId);
        if ($post) {
            return ResponseService::success(
                new PostResource($post)
            );
        }
        return ResponseService::notFound();
    }

    /**
     * @param array $postParams
     * @return JsonResponse
     */
    public function store(array $postParams): JsonResponse
    {
        $newPost = Post::create($postParams);
        return ResponseService::success(
            new PostResource($newPost)
        );
    }

    /**
     * @param string $postId
     * @param array $validated
     * @return JsonResponse
     */
    public function update(string $postId, array $validated): JsonResponse
    {
        $posts = Post::findOrFail($postId);
        if ($posts && $updated = $posts->update($validated)) {
            return ResponseService::success(new PostResource($posts));
        }
        return ResponseService::notFound();
    }

    /**
     * @param string $postId
     * @return mixed
     */
    public function destroy(string $postId)
    {
        if($post = Post::find($postId)){
            if($post->delete()){
                return ResponseService::success();
            }
            return ResponseService::unSuccess();
        }
        return ResponseService::notFound();
    }
}
