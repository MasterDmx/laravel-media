<?php

namespace MasterDmx\LaravelMedia;

use ErrorException;
use MasterDmx\LaravelMedia\Entities\MediaCollection;
use MasterDmx\LaravelMedia\Services\Uploader;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use MasterDmx\LaravelMedia\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaManager
{
    const DEFAULT_TYPE = 'file';
    const DEFAULT_HANDLER = 'file';

    private $typeEntities = [];

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

        $this->typeEntities['file'] = config('media.default_entity');

        foreach (config('media.types', []) as $key => $data) {
            $this->typeEntities[$key] = $data['entity'] ?? config('media.default_entity');
        }
    }

    /**
     * Импорт коллекции изображений
     *
     * @param array $data
     * @return MediaCollection
     */
    public function import(array $data): MediaCollection
    {
        $list = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = ['path' => $value];
            }

            $type = !empty($value['type']) ? $value['type'] : 'file';
            $list[$key] = $this->typeEntities[$type]::import($value + [
                'id' => $key,
                'type' => $type
            ]);
        }

        return new MediaCollection($list);
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
    public function addFromUrl(string $url, string $name = null, bool $disableDuplicates = true)
    {
        // Проверяем существование ранее импортированного файла
        if ($disableDuplicates && $model = $this->model->importedTo($url)->first()) {
            return $model;
        }

        // Загрузить файл
        $file = $this->uploader->uploadFromUrl($url);

        // Запись в модели
        $model = $this->model->add(
            $file->getMediaPath(),
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
