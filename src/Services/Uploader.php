<?php

namespace MasterDmx\LaravelMedia\Services;

use ErrorException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use MasterDmx\LaravelMedia\Entities\File;

class Uploader
{
    /**
     * Загрузка
     *
     * @param \Illuminate\Http\UploadedFile $file
     */
    public function upload(UploadedFile $file): File
    {
        $oldName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = $this->generateName();
        $catalogs = $this->generateCatalogsPath($name);
        $path = $catalogs . DIRECTORY_SEPARATOR . $name . '.' . $file->extension();

        if (Storage::disk('media')->putFileAs($catalogs, $file, $name . '.' . $file->extension())) {
            return new File($path, $oldName);
        }

        throw new ErrorException('File not uploaded');
    }

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
        $name = $this->generateName();
        $path = $this->generateCatalogsPath($name) . DIRECTORY_SEPARATOR . $name . '.' . $extension;

        if (Storage::disk('media')->put($path, $content)) {
            return new File($path, $oldName);
        }

        throw new ErrorException('File not uploaded');
    }

    // ---------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------

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
    private function generateCatalogsPath(string $fileName, int $symbolCount = 2): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            date('Y'),
            date('m'),
            substr($fileName, 0, $symbolCount)
        ]);
    }
}
