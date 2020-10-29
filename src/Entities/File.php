<?php

namespace MasterDmx\LaravelMedia\Entities;

use Illuminate\Http\File as LocalFile;
use Illuminate\Support\Facades\Storage;

class File
{
    /**
     * Локальный путь относительно media-директории
     *
     * @var string
     */
    private $path;

    /**
     * Старое название файла
     *
     * @var string
     */
    private $oldName;

    /**
     * Файл
     *
     * @var \Illuminate\Http\File
     */
    private $file;

    public function __construct(string $path, string $oldName = null)
    {
        $this->path = $path;
        $this->oldName = $oldName;
    }

    /**
     * Получить полный путь до файла
     *
     * @return string
     */
    public function getPath(): string
    {
        return Storage::disk('media')->path($this->path);
    }

    /**
     * Получить путь до файла в рамках медиа-директории
     *
     * @return string
     */
    public function getMediaPath(): string
    {
        return $this->path;
    }

    /**
     * Получить расширение файла
     *
     * @return string
     */
    public function getExtension(): string
    {
        return $this->getFile()->extension();
    }

    /**
     * Получить объект файла на сервере
     *
     * @return \Illuminate\Http\File
     */
    public function getFile(): LocalFile
    {
        if (!isset($this->file)) {
            return $this->file = new LocalFile($this->getPath());
        }

        return $this->file;
    }

    /**
     * Получить старое название
     *
     * @param string $default
     * @return string|null
     */
    public function getOldName(string $default = null): ?string
    {
        return $this->oldName ?? $default;
    }
}
