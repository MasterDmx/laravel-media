<?php

namespace MasterDmx\LaravelMedia;

use ErrorException;
use Illuminate\Filesystem\FilesystemAdapter;
use MasterDmx\LaravelMedia\Entities\MediaCollection;
use MasterDmx\LaravelMedia\Services\Uploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MasterDmx\LaravelMedia\Models\Media;

class MediaManager
{
    /**
     * Тип по умолчанию
     */
    const DEFAULT_TYPE = 'file';

    /**
     * Обработчик по умолчанию
     */
    const DEFAULT_HANDLER = MasterDmx\LaravelMedia\Entities\Media\File::class;

    /**
     * Загрузчик
     *
     * @var \MasterDmx\LaravelMedia\Services\Uploader
     */
    private $uploader;

    /**
     * Загрузчик
     *
     * @var \MasterDmx\LaravelMedia\Models\Media
     */
    private $model;

    public function __construct(Uploader $uploader, Media $model)
    {
        $this->uploader = $uploader;
        $this->model = $model;
    }

    /**
     * Импорт коллекции изображений
     *
     * @param array $data
     * @return MediaCollection
     */
    public function createCollection(array $data): MediaCollection
    {
        foreach ($data as $key => $content) {
            if (is_string($content)) {
                $content = ['path' => $content];
            }

            $list[$key] = $this->createInstance($content['path'], (!empty($content['type']) ? $content['type'] : static::DEFAULT_TYPE), $key, $content);
        }

        return new MediaCollection($list ?? []);
    }

    /**
     * Создать экземпляр обработчика медиа файла
     *
     * @param integer $id
     * @param string $type
     * @param string $path
     * @param array $extra
     * @return void
     */
    public function createInstance(string $path, string $type = null, string $key = null, array $extra = [])
    {
        return $this->getHandler($type)::instance([
            'key' => $key,
            'type' => $type,
            'path' => $path,
        ] + $extra);
    }

    /**
     * Загрузить файл по внешней ссылке
     */
    public function addFile(UploadedFile $uploadedFile)
    {
        // Загрузить файл
        $file = $this->uploader->upload($uploadedFile);

        // Запись в модели
        $model = $this->model->add(
            $file->getResidualUrl(),
            $this->getType($file->getExtension()),
            $name ?? $file->getOldName(),
            null
        );

        if ($model->id > 0) {
            return $model;
        }

        throw new ErrorException('Model error');
    }

    /**
     * Загрузить файл по внешней ссылке
     */
    public function addFileFromUrl(string $url, string $name = null, bool $disableDuplicates = true)
    {
        // Проверяем существование ранее импортированного файла
        if ($disableDuplicates && $model = $this->model->importedTo($url)->first()) {
            return $model;
        }

        // Загрузить файл
        $file = $this->uploader->uploadFromUrl($url);

        // Запись в модели
        $model = $this->model->add(
            $file->getResidualUrl(),
            $this->getType($file->getExtension()),
            $name ?? $file->getOldName(),
            null,
            $url
        );

        if ($model->id > 0) {
            return $model;
        }

        throw new ErrorException('Model error');
    }

    /**
     * Получить модель
     *
     * @return \MasterDmx\LaravelMedia\Models\Media
     */
    public function getModel(): Media
    {
        return $this->model;
    }

    /**
     * Получить хранилище
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public function getStorage(): FilesystemAdapter
    {
        return Storage::disk(config('media.disk'));
    }

    /**
     * Получить тип по расширению
     *
     * @param string $extension
     * @return string|null
     */
    private function getType(string $extension): ?string
    {
        foreach (config('media.types', []) as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return $type;
            }
        }

        return config('media.default_type', static::DEFAULT_TYPE);
    }

    /**
     * Получить обработчик по типу
     *
     * @param string $type
     * @return string
     */
    private function getHandler(string $type): string
    {
        if ($handler = config('media.handlers.' . $type, null)) {
            return $handler;
        }

        return config('media.handlers.default', static::DEFAULT_HANDLER);
    }

}
