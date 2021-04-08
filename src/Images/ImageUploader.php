<?php

namespace MasterDmx\LaravelMedia\Images;

use Carbon\Carbon;
use Intervention\Image\ImageManager;
use MasterDmx\LaravelMedia\Id;
use MasterDmx\LaravelMedia\Contexts\Context;
use MasterDmx\LaravelMedia\Contexts\ContextRegistry;
use MasterDmx\LaravelMedia\Images\Variations\ImageVariator;
use MasterDmx\LaravelMedia\MediaService;
use MasterDmx\LaravelMedia\MetaStorage;

final class ImageUploader
{
    private ImageManager $imageManager;
    private MediaService $service;
    private ContextRegistry $contexts;

    public function __construct(ImageManager $imageManager, MediaService $service, ContextRegistry $contexts)
    {
        $this->imageManager = $imageManager;
        $this->service = $service;
        $this->contexts = $contexts;
    }

    /**
     * Загрузить изображение на сервер
     *
     * @param                   $data
     * @param Context           $context
     * @param string            $name
     * @param string            $title
     *
     * @return Image
     */
    public function upload($data, Context $context, string $name, string $title = ''): Image
    {
        // Получаем объект Image из пакета Intervention
        $image = $this->imageManager->make($data);

        // Получаем расширение по MIME типу
        $extension = $this->service->getExtensionByMime($image->mime());

        // Валидация по контексту
        ImageValidator::start($context, $image);

        // Генерируем ID
        $id = $this->service->generateId($extension);

        // Создаем хранилище на диске по алиасу
        $storage = $this->service->makeStorage($id, true);

        // Сохраняем файл изображения
        $image->save($storage->getPathToFile());

        // Создаем вариации
        $variations = ImageVariator::init($storage)->createFor($context, $image)->getAll();

        // Мета хранилище
        $metaStorage = MetaStorage::init($storage);

        // Устаналиваем мета
        $meta = ImageMeta::fromArray($metaStorage->all());
        $meta->mime = $image->mime();
        $meta->uploadedAt = Carbon::now();
        $meta->name = $name;
        $meta->title = $title;
        $meta->width = $image->width();
        $meta->height = $image->height();

        // Сохраняем мета в хранилище
        $metaStorage->import($meta->toArray())->save();

        return new Image($id, $storage, $meta, $variations);
    }
}
