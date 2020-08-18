<?php

namespace MasterDmx\LaravelMedia;

use MasterDmx\LaravelMedia\Entities\MediaCollection;

class MediaManager
{
    private $typeEntities = [];

    public function __construct()
    {
        $this->typeEntities['file'] = config('media.default_entity');

        foreach (config('media.types', []) as $key => $data) {
            $this->typeEntities[$key] = $data['entity'] ?? config('media.default_entity');
        }
    }

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

}
