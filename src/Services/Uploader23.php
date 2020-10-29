<?php

namespace MasterDmx\LaravelMedia\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Uploader23
{
    const DISK = 'media';

    /**
     * Статус заливки
     *
     * @var boolean
     */
    public $uploaded = false;

    /**
     * Файл
     *
     * @var UploadedFile
     */
    public $file;

    /**
     * Имя файла
     *
     * @var string
     */
    public $fileName;

    /**
     * Расширение файла
     *
     * @var string
     */
    public $fileExtension;

    /**
     * Каталоги
     *
     * @var array
     */
    public $catalogs;

    /**
     * Расширения для определения типов
     *
     * @var array
     */
    private $typeExtensions = [
        'image' => [
            'png',
            'jpg',
            'jpeg'
        ],

        'document' => [
            'pdf',
        ],
    ];

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
        $this->fileName = $this->generateFileName();
        $this->catalogs = $this->generateCatalogsByFileName($this->fileName);
    }

    /**
     * Загрузка изображения
     *
     * @return bool
     */
    public function upload(): bool
    {
        if (Storage::disk(static::DISK)->putFileAs($this->getCatalogPath(), $this->file, $this->getFullFileName())) {
            return $this->uploaded = true;
        }

        return false;
    }

    /**
     * Получить полное имя файла
     *
     * @return string
     */
    public function getFullFileName(): string
    {
        return $this->fileName . '.' . $this->getExtension();
    }

    /**
     * Получить URI строку пути до файла
     *
     * @return string
     */
    public function getUriPath(): string
    {
        return implode('/', $this->catalogs) . '/' . $this->getFullFileName();
    }

    /**
     * Получить путь по каталогам до файла
     *
     * @return string
     */
    public function getCatalogPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, $this->catalogs);
    }

    public function getExtension()
    {
        return $this->file->extension();
    }

    /**
     * Установка типа
     *
     * @return string
     */
    public function getType(): string
    {
        if (in_array($this->getExtension(), $this->typeExtensions['image'])) {
            return 'image';
        } elseif (in_array($this->getExtension(), $this->typeExtensions['document'])) {
            return 'document';
        }

        return 'file';
    }

    /**
     * Сгенерировать название для БД
     *
     * @return string
     */
    public function getOriginalName(): string
    {
        return pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    /**
     * Сгенерировать название файла для хранения
     *
     * @return string
     */
    private function generateFileName(): string
    {
        return substr(md5(microtime() . rand(0, 1000)), 0, 15);
    }

    /**
     * Сгенерировать цепочку каталогов по схеме Год Месяц *2 первых символа от строки*
     *
     * @return array
     */
    private function generateCatalogsByFileName(string $string, int $symbolCount = 2): array
    {
        return [
            date('Y'),
            date('m'),
            substr($string, 0, $symbolCount)
        ];
    }
}
