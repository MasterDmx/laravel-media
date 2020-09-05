<?php

namespace MasterDmx\LaravelMedia\Casts;

use ErrorException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use MasterDmx\LaravelMedia\Entities\MediaCollection;
use MasterDmx\LaravelMedia\MediaManager;

/**
 * Преобразование JSON \ Массива в коллекцию и обратно
 */
class MediaCast implements CastsAttributes
{
    /**
     * Менеджер
     *
     * @var MediaManager
     */
    private $manager;

    public function __construct()
    {
        $this->manager = app(MediaManager::class);
    }

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        return $this->manager->import(json_decode($value, true), get_class($model));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string  $key
     * @param  array|string|\MasterDmx\LaravelMedia\Entities\MediaCollection $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_a($value, MediaCollection::class)) {
            return json_encode($value->export());
        }

        if (is_array($value)) {
            return json_encode($this->manager->import($value)->export());
        }

        throw new ErrorException('Undefined value type');
    }
}
