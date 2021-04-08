<?php

namespace MasterDmx\LaravelMedia;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use MasterDmx\LaravelMedia\Exceptions\UndefinedValueForCastException;

class MediaCast implements CastsAttributes
{
    private MediaService $service;

    public function __construct()
    {
        $this->service = app(MediaService::class);
    }

    public function get($model, $key, $value, $attributes)
    {
        return $this->service->getCollection(json_decode($value, true));
    }

    public function set($model, $key, $value, $attributes)
    {
        if (is_a($value, MediaCollection::class)) {
            return json_encode($value->export());
        }

        if (is_array($value)) {
            return json_encode($this->service->getCollection($value)->export());
        }

        throw new UndefinedValueForCastException('Undefined value type');
    }
}
