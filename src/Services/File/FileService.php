<?php

namespace Valibool\TelegramConstruct\Services\File;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;

class FileService
{
    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var Filesystem
     */
    protected $storage;

    /**
     * @var string
     */
    protected $disk;

    /**
     * @var string|null
     */
    protected $group;

    /**
     * @var Generator
     */
    protected $engine;

    /**
     * @var bool
     */
    protected $duplicate = false;
    public function __construct(UploadedFile $file, string $disk = null, string $group = null)
    {
        abort_if($file->getSize() === false, 415, 'File failed to load.');

        $this->file = $file;
        $this->disk = $disk ?? 'public';
        $this->storage = Storage::disk($this->disk);

        /** @var string $generator */
        $generator = Generator::class;

        $this->engine = new $generator($file);
        $this->group = $group;
        $this->customFilePath = false;
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     *
     * @return Model|TgConstructAttachment
     */
    public function load($customFilePath = false): Model
    {
        if ($customFilePath){
            $this->customFilePath = $customFilePath;
        }
        $attachment = $this->getMatchesHash();

        if ($attachment === null) {

            return $this->save();
        }

        $attachment = $attachment->replicate()->fill([
            'original_name' => $this->file->getClientOriginalName(),
            'sort'          => 0,
            'user_id'       => Auth::id(),
            'group'         => $this->group,
        ]);

        $attachment->save();


        return $attachment;
    }
    /**
     * @return TgConstructAttachment|null
     */
    private function getMatchesHash()
    {

        //check unique file
        if ($this->duplicate) {
            return null;
        }
    }

    /**
     * @return Model
     */
    private function save(): Model
    {
        $path = $this->engine->path();
        if($this->customFilePath){
            $path = $this->customFilePath;
        }
//        $this->storage->putFileAs($this->engine->path(), $this->file, $this->engine->fullName(), [
        $this->storage->putFileAs($path, $this->file, $this->engine->fullName(), [
            'mime_type' => $this->engine->mime(),
        ]);

        $attachment = TgConstructAttachment::create([
            'name' => $this->engine->name(),
            'mime' => $this->engine->mime(),
            'hash' => $this->engine->hash(),
            'extension' => $this->engine->extension(),
            'original_name' => $this->file->getClientOriginalName(),
            'size' => $this->file->getSize(),
//            'path' => Str::finish($this->engine->path(), '/'),
            'path' => Str::finish($path, '/'),
            'disk' => $this->disk,
            'group' => $this->group,
            'user_id' => Auth::user()->id ?? null,
        ]);

        return $attachment;
    }

}
