<?php

namespace MasterDmx\LaravelMedia\Services;

use ErrorException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use MasterDmx\LaravelMedia\Entities\File;

class Uploader
{
    /**
     * Загрузка из урла
     *
     * @param string $url
     */
    public function uploadFromUrl(string $url): File
    {
        $info = pathinfo($url);
        $extension = $info['extension'];
        $oldName = $info['filename'];

        // Замена JPG на JPEG
        if ($extension === 'jpg') {
            $extension = 'jpeg';
        }

        // Получение файла
        $response = Http::withOptions(['verify' => false])->get($url);

        if (!$response->ok()) {
            throw new ErrorException('File not found from url');
        }

        $content = $response->body();
        $path = $this->generateFilePath($extension);

        if (Storage::disk('media')->put($path, $content)) {
            return new File($path, $oldName);
        }

        throw new ErrorException('File not uploaded');
    }

    // ---------------------------------------------------------
    // System
    // ---------------------------------------------------------

    /**
     * Сгенерировать название файла для хранения
     *
     * @return string
     */
    private function generateFilePath(string $extension): string
    {
        $name = $this->generateName();
        return implode(DIRECTORY_SEPARATOR, $this->generateCatalogs($name)) . DIRECTORY_SEPARATOR . $name . '.' . $extension;
    }

    /**
     * Сгенерировать название файла для хранения
     *
     * @return string
     */
    private function generateName(): string
    {
        return substr(md5(microtime() . rand(0, 1000)), 0, 15);
    }

    /**
     * Сгенерировать цепочку каталогов по схеме Год Месяц *2 первых символа от строки*
     *
     * @return array
     */
    private function generateCatalogs(string $fileName, int $symbolCount = 2): array
    {
        return [
            date('Y'),
            date('m'),
            substr($fileName, 0, $symbolCount)
        ];
    }
}
