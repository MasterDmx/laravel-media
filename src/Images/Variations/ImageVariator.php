<?php

namespace MasterDmx\LaravelMedia\Images\Variations;

use Intervention\Image\Image;
use MasterDmx\LaravelMedia\Contexts\Context;
use MasterDmx\LaravelMedia\MediaStorage;

class ImageVariator
{
    public const DIRECTORY = 'variations';

    private MediaStorage $storage;

    public function __construct(MediaStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Инициализирует объект вариатора
     *
     * @param MediaStorage $storage
     *
     * @return ImageVariator
     */
    public static function init(MediaStorage $storage): ImageVariator
    {
        return new static($storage);
    }

    /**
     * Создает вариции изображений по контексту
     *
     * @param Context $context
     * @param Image   $image
     *
     * @return ImageVariator
     */
    public function createFor(Context $context, Image $image): ImageVariator
    {
        foreach ($this->initVariations($context->variations()) as $variation) {
            $this->createIfNotHas($variation, $image);
        }

        return $this;
    }

    /**
     * Создает вариацию
     *
     * @param \MasterDmx\LaravelMedia\Images\Variations\ImageVariationConfig $variation
     * @param Image                                                          $image
     */
    public function create(ImageVariationConfig $variation, Image $image): void
    {
        // Проверка по условиям
        if ($variation->condition($image)) {
            // Создание директории, если ее нет
            $this->storage->createDirectoryIfNotHas(static::DIRECTORY);

            // Клонирование объекта
            $image = clone $image;

            // Применение фильтра + сохранение
            $image->filter($variation)->save($this->getVariationPath($variation));
        }
    }

    /**
     * Создает вариацию, если ее нет
     *
     * @param \MasterDmx\LaravelMedia\Images\Variations\ImageVariationConfig $variation
     * @param Image                                                          $image
     */
    public function createIfNotHas(ImageVariationConfig $variation, Image $image)
    {
        if (!$this->has($variation)) {
            $this->create($variation, $image);
        }
    }

    /**
     * Проверяет наличие вариации
     *
     * @param \MasterDmx\LaravelMedia\Images\Variations\ImageVariationConfig $variation
     *
     * @return bool
     */
    public function has(ImageVariationConfig $variation): bool
    {
        return $this->storage->has(static::DIRECTORY . DIRECTORY_SEPARATOR . $this->getVariationFileName($variation));
    }

    /**
     * Получает коллекцию вариаций
     */
    public function getAll(): ImageVariationCollection
    {
        $instances = [];

        foreach ($this->storage->getFilesIn(static::DIRECTORY) as $file) {
            $parsed = explode('.', $file);
            $instances[$parsed[0]] = new ImageVariation($parsed[0], $parsed[1], $this->storage->getUrlTo(static::DIRECTORY . '/' .$file));
        }

        return new ImageVariationCollection($instances);
    }

    /**
     * Получить полное название файла вариации
     *
     * @param \MasterDmx\LaravelMedia\Images\Variations\ImageVariationConfig $variation
     *
     * @return string
     */
    public function getVariationFileName(ImageVariationConfig $variation): string
    {
        return $variation->name() . '.' . $this->storage->getExtension();
    }

    /**
     * Получить путь до вариации
     *
     * @param ImageVariationConfig $variation
     *
     * @return string
     */
    private function getVariationPath(ImageVariationConfig $variation): string
    {
        return $this->storage->getPathTo(static::DIRECTORY . DIRECTORY_SEPARATOR . $this->getVariationFileName($variation));
    }

    /**
     * Инициализирует объекты вариаций, используя контейнер Laravel
     *
     * @param array $classes
     *
     * @return ImageVariationConfig[]
     */
    private function initVariations(array $classes): array
    {
        $variations = [];

        foreach ($classes as $class) {
            $variations[] = app($class);
        }

        return $variations;
    }
}
