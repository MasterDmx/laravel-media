<?php

namespace MasterDmx\LaravelMedia;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class MetaStorage
{
    /**
     * Объект медиа хранилища
     *
     * @var MediaStorage
     */
    private MediaStorage $storage;

    /**
     * Данные
     *
     * @var array
     */
    private array $data = [];

    public function __construct(MediaStorage $storage)
    {
        $this->storage = $storage;

        if ($this->storage->has($this->getMetaFileFullName())) {
            $this->data = json_decode($this->storage->get($this->getMetaFileFullName()), true);
        }
    }

    /**
     * Создает объект мета хранилища
     *
     * @param MediaStorage $storage
     *
     * @return static
     */
    public static function init(MediaStorage $storage): self
    {
        return new static($storage);
    }

    /**
     * Сохранить данные в файл
     */
    public function save(): bool
    {
        return $this->storage->put($this->getMetaFileFullName(), json_encode($this->data));
    }

    /**
     * Положить значения из массива
     *
     * @param array $data
     *
     * @return $this
     */
    public function putFromArray(array $data): MetaStorage
    {
        foreach ($data as $key => $value){
            $this->put($key, $value);
        }

        return $this;
    }

    /**
     * Импортирует параметры из массива
     *
     * @param array $data
     *
     * @return $this
     */
    public function import(array $data): MetaStorage
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Внести параметр
     *
     * @param $key
     * @param $value
     *
     * @return MetaStorage
     */
    public function put($key, $value): MetaStorage
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Получить конкретный параметр
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Проверить наличие параметр
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Получить все данные
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Получить полное названи МЕТА-файла
     *
     * @return string
     */
    public function getMetaFileFullName(): string
    {
        return config('media.meta_file_name') . '.json';
    }
}
