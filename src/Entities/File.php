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
     * Локальный URL путь относительно media-директории
     *
     * @var string
     */
    private $urlPath;

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
        $this->path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $this->urlPath = str_replace('\\', '/', $path);
        $this->oldName = $oldName;
    }

    /**
     * Получить полный путь до файла
     *
     * @return string
     */
    public function getPath(): string
    {
        return Storage::disk(config('media.disk'))->path($this->path);
    }

    /**
     * Получить путь до файла в рамках медиа-директории
     *
     * @return string
     */
    public function getResidualPath(): string
    {
        return $this->path;
    }

    /**
     * Получить поленый URL
     *
     * @return string URL
     */
    public function getUrl()
    {
        return Storage::disk(config('media.disk'))->url($this->urlPath);
    }

    /**
     * Получить URL в рамках медиа-раздела
     *
     * @return string URL
     */
    public function getResidualUrl()
    {
        return $this->urlPath;
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

    public function remove()
    {
        return Storage::disk(config('media.disk'))->delete($this->path);
    }

    // ------------------------------------------------------------
    //
    // ------------------------------------------------------------
}
