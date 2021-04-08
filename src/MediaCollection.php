<?php

namespace MasterDmx\LaravelMedia;

use Illuminate\Support\Collection;
use MasterDmx\LaravelMedia\Images\Image;

/**
 * Class MediaCollection
 *
 * @package MasterDmx\LaravelMedia
 */
class MediaCollection extends Collection
{
    /**
     * Сортирует коллекцию по отметке времени загрузки
     *
     * @return MediaCollection
     */
    public function sortByUpload(): MediaCollection
    {
        return $this->sortBy(fn ($media) => -$media->uploadedAt()->timestamp);
    }

    /**
     * Возвращает массив с данными для хранения
     *
     * @return array
     */
    public function export(): array
    {
        return $this->map(fn ($item) => $item->export())->toArray();
    }

    /**
     * Использует шаблон $template для представления данных.
     * Если сущности по $alias не найдено, вернет $default
     *
     * @param string      $alias
     * @param string|null $template
     * @param string|null $default
     *
     * @return string|null
     */
    public function performance(string $alias, ?string $template = null, ?string $default = null): ?string
    {
        return $this->has($alias) ? $this->get($alias)->performance($template) : $default;
    }

    /**
     * Если медиа есть в коллекции
     *
     * @param string $id
     * @param mixed  $than
     * @param mixed  $else
     *
     * @return mixed|null
     */
    public function ifHas(string $id, $than, $else = null)
    {
        if ($this->has($id)) {
            if (is_callable($than)) {
                return $than($this->get($id));
            } else {
                return $than;
            }
        }

        return $else;
    }

    /**
     * Получить объект изображения
     *
     * @param string $id
     *
     * @return Image
     */
    public function getImage(string $id): Image
    {
        return $this->get($id);
    }
}
