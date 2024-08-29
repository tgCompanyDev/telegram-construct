<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use Illuminate\Http\Request;
use Valibool\TelegramConstruct\Services\File\FileUploader;

class UploadFileController extends Controller
{
    public function __construct(protected FileUploader  $fileUploader)
    {
    }

    /**
     * @param int $message_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadImage(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|file|mimes:jpg,bmp,png|max:2800',
        ]);

        return $this->fileUploader->loadImage($validated['image']);
    }
}
