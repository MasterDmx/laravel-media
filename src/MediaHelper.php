<?php

namespace MasterDmx\LaravelMedia;

use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    /**
     * Получить URL файла по пути
     *
     * @param string $path
     * @return string URL
     */
    public static function getUrl(string $path)
    {
        return Storage::disk(config('media.disk'))->url($path);
    }
}
