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
            $keyPars = explode('/', $key);
            $list[$keyPars[1]] = $this->typeEntities[$keyPars[0]]::import($value + [
                'id' => $keyPars[1],
                'type' => $keyPars[0]
            ]);
        }

        return new MediaCollection($list);
    }

}
