<?php

namespace MasterDmx\LaravelMedia;

use Intervention\Image\ImageManager;
use MasterDmx\LaravelMedia\Contexts\ContextRegistry;
use MasterDmx\LaravelMedia\Exceptions\MediaNotFoundException;
use MasterDmx\LaravelMedia\Exceptions\UndefinedMimeTypeException;
use MasterDmx\LaravelMedia\Images\Image;
use MasterDmx\LaravelMedia\Images\ImageMeta;
use MasterDmx\LaravelMedia\Images\Variations\ImageVariator;

class MediaService
{
    private ContextRegistry $contexts;
    private ImageManager $imageManager;

    /**
     * MediaService constructor.
     *
     * @param ContextRegistry $contexts
     * @param ImageManager    $imageManager
     */
    public function __construct(ContextRegistry $contexts, \Intervention\Image\ImageManager $imageManager)
    {
        $this->contexts = $contexts;
        $this->imageManager = $imageManager;
    }

    /**
     * Получить медиа сущность по обозначению
     *
     * @param string      $id
     * @param string|null $personalTitle
     *
     * @return Image
     */
    public function getMedia(string $id, string $personalTitle = null): Image
    {
        $id = Id::parse($id);
        $storage = MediaStorage::init($id);

        if (!$storage->hasDefaultFile()) {
            throw new MediaNotFoundException('Media by alias ' . $id->toString() . ' not found');
        }

        $meta       = ImageMeta::fromArray(MetaStorage::init($storage)->all());
        $variations = ImageVariator::init($storage)->getAll();

        return new Image($id, $storage, $meta, $variations, $personalTitle);
    }

    /**
     * Удалить медиа по ID
     *
     * @param string $id
     *
     * @return bool
     */
    public function removeMedia(string $id): bool
    {
        return MediaStorage::init(Id::parse($id))->remove();
    }

    /**
     * Обновить МЕТА информацию
     *
     * @param        $id
     * @param string $name
     * @param string $title
     *
     * @return bool
     */
    public function updateMedia($id, string $name = '', string $title = ''): bool
    {
        $id          = Id::parse($id);
        $storage     = MediaStorage::init($id);
        $metaStorage = MetaStorage::init($storage);
        $meta        = ImageMeta::fromArray($metaStorage->all());
        $meta->name  = $name;
        $meta->title = $title;

        return $metaStorage->import($meta->toArray())->save();
    }

    /**
     * Получить медиа-коллекцию
     *
     * @param array|null $data
     * @param bool       $useAliasForKey
     *
     * @return MediaCollection
     */
    public function getCollection(array $data = null, bool $useAliasForKey = false): MediaCollection
    {
        $instances = [];

        foreach ($data ?? [] as $key => $item){
            try {
                $media = $this->getMedia(is_string($item) ? $item : $item['id'] ?? '', trim($item['title'] ?? ''));

                if ($useAliasForKey) {
                    $instances[$media->getId()->toString()] = $media;
                } elseif (isset($data['field'])) {
                    $instances[$data['field']] = $media;
                } else {
                    $instances[$key] = $media;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return new MediaCollection($instances);
    }

    /**
     * Получить все медиа файлы
     *
     * @return MediaCollection
     */
    public function getAllMedia(): MediaCollection
    {
        return $this->getCollection($this->scanDisk(), true);
    }

    /**
     * Создает объект персонального медиа хранилища
     *
     * @param Id   $id
     * @param bool $createDirectories Создать директории, если их нет
     *
     * @return MediaStorage
     */
    public function makeStorage(Id $id, bool $createDirectories = false): MediaStorage
    {
        $storage = new MediaStorage($id);

        if ($createDirectories && !$storage->checkExists()) {
            $storage->createStorage();
        }

        return $storage;
    }

    /**
     * Определяет расширение файла по MIME типу
     *
     * @param string $mime
     *
     * @return string
     * @throws UndefinedMimeTypeException
     */
    public function getExtensionByMime(string $mime): string
    {
        $mimeByExtensions = config('media.extensions');

        if (!isset($mimeByExtensions[$mime])) {
            throw new UndefinedMimeTypeException('It is impossible to determine the file extension, since an unknown MIME type has come');
        }

        return $mimeByExtensions[$mime];
    }

    /**
     * Рекурсивно генерирует ID, проверяя уже имеющиеся
     * Позволяет избежать одинаковых ID
     *
     * @param string $extension
     *
     * @return Id
     */
    public function generateId(string $extension): Id
    {
        $id = Id::generate($extension);

        while (MediaStorage::init($id)->checkExists()) {
            $id = Id::generate($extension);
        }

        return $id;
    }

    /**
     * Применить контекст
     *
     * @param string $id
     * @param string $context
     *
     * @return Image
     */
    public function applyContext(string $id, string $context): Image
    {
        $id = Id::parse($id);
        $storage = MediaStorage::init($id);
        $context = $this->contexts->get($context);
        $image = $this->imageManager->make($storage->getPathToFile());

        ImageVariator::init($storage)->createFor($context, $image);

        return $this->getMedia($id->toString());
    }

    /**
     * Просканировать диск на наличие изображений
     *
     * @param string|null $directory
     * @param int         $level
     *
     * @return array
     */
    public function scanDisk(?string $directory = null, int $level = 1): array
    {
        $result = [];
        $nextLevel = $level + 1;
        $directory = $directory ?? '';

        foreach (scandir(MediaStorage::getDisk()->path($directory)) as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            if ($level === 4) {
                $result[] = $name;
            } else {
                $result = array_merge($result, $this->scanDisk($directory . DIRECTORY_SEPARATOR . $name, $nextLevel));
            }
        }

        return $result;
    }

}

