<?php

namespace MasterDmx\LaravelMedia;

use MasterDmx\LaravelMedia\Contexts\ContextRegistry;
use MasterDmx\LaravelMedia\Events\MediaUploaded;
use MasterDmx\LaravelMedia\Images\ImageUploader;
use MasterDmx\LaravelMedia\Images\Image;

class MediaManager
{
    private ImageUploader $imageUploader;
    private MediaService $service;
    private ContextRegistry $contexts;

    /**
     * ImageStorage constructor.
     *
     * @param ImageUploader   $imageUploader
     * @param MediaService    $service
     * @param ContextRegistry $contexts
     */
    public function __construct(ImageUploader $imageUploader, MediaService $service, Contexts\ContextRegistry $contexts)
    {
        $this->imageUploader = $imageUploader;
        $this->service = $service;
        $this->contexts = $contexts;
    }

    /**
     * Получить все изображения
     *
     * @return MediaCollection
     */
    public function getAll(): MediaCollection
    {
        return $this->service->getAllMedia();
    }

    /**
     * Получить изображение по обозначению
     *
     * @param string $id
     *
     * @return Image
     */
    public function get(string $id): Image
    {
        return $this->service->getMedia($id);
    }

    /**
     * Применить контекст
     *
     * @param string $id
     * @param string $context
     *
     * @return Image
     */
    public function applyContext(string $id, string $context = 'default'): Image
    {
        return $this->service->applyContext($id, $context);
    }

    /**
     * Добавить изображение
     *
     * @param                   $data
     * @param string|null       $context
     * @param string            $name
     * @param string            $title
     *
     * @return Image
     */
    public function add($data, string $context = 'default', string $name = 'Без названия', string $title = ''): Image
    {
        return $this->imageUploader->upload($data, $this->contexts->get($context), $name, $title);
    }

    /**
     * Проверяет наличие медиа по алиасу
     *
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return MediaStorage::init(Id::parse($id))->hasFile();
    }

    /**
     * Удаляет медиа с сервера
     *
     * @param string $id
     *
     * @return bool
     */
    public function remove(string $id): bool
    {
        return $this->service->removeMedia($id);
    }

    /**
     * Формирует медиа коллекцию, заполняя ее значениями из $import
     *
     * @param array $import
     *
     * @return MediaCollection
     */
    public function getCollection(array $import): MediaCollection
    {
        return $this->service->getCollection($import);
    }

    /**
     * Изменяет МЕТА информацию файла
     *
     * @param string $id
     * @param string $name
     * @param string $title
     *
     * @return bool
     */
    public function updateMeta(string $id, string $name, string $title): bool
    {
        return $this->service->updateMedia($id, $name, $title);
    }
}
