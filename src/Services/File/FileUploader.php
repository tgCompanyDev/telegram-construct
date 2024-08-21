<?php

namespace Valibool\TelegramConstruct\Services\File;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Services\Response\ResponseService;

class FileUploader
{
    const ROOT_FOLDER = 'telegram-construct/images';
    private string $customFilePath;

    public function loadImage(mixed $image)
    {
        $this->customFilePath = self::ROOT_FOLDER;

        $loadedFile = $this->loadFile($image);
        if ($loadedFile->url) {

            return ResponseService::success([
                'file_id' => $loadedFile->id,
                'url' => $loadedFile->url,
            ]);
        }
        return ResponseService::unsuccess($loadedFile);

    }

    public function loadFile(mixed $file): Model|TgConstructAttachment
    {
        $fileService = new FileService($file);
        return $fileService->load($this->customFilePath);
    }
}
