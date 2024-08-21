<?php

namespace Valibool\TelegramConstruct\Services\File;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Generator
{
    /**
     * @var MimeTypes
     */
    protected MimeTypes $mimes;
    /**
     * @var UploadedFile
     */
    protected UploadedFile $file;
    /**
     * @var string
     */
    protected $uniqueId;

    /**
     * @var ?string
     */
    protected $path;

    /**
     * @var int
     */
    protected $time;

    /**
     * Generator constructor.
     *
     * @param UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
        $this->path = null;
        $this->time = time();
        $this->mimes = new MimeTypes();
        $this->uniqueId = uniqid('', true);
    }

    /**
     * @return string
     */
    public function mime(): string
    {
        return $this->mimes->getMimeType($this->extension())
            ?? $this->mimes->getMimeType($this->file->getClientMimeType())
            ?? 'unknown';
    }

    /**
     * @return string
     */
    public function extension(): string
    {
        $extension = $this->file->getClientOriginalExtension();

        return empty($extension)
            ? $this->mimes->getExtension($this->file->getClientMimeType(), 'unknown')
            : $extension;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return sha1($this->uniqueId.$this->file->getClientOriginalName());
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return Str::finish($this->name(), '.').$this->extension();
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->path ?? date('Y/m/d', $this->time());
    }

    /**
     * @param string|null $path
     * @return $this
     */
    public function setPath(?string $path = null)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return int
     */
    public function time(): int
    {
        return $this->time;
    }

    /**
     * @return false|string
     */
    public function hash()
    {
        return sha1_file($this->file->path());
    }
}
