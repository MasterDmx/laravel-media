<?php

namespace MasterDmx\LaravelMedia\Services;

class TypeQualifier
{
    public function spot(string $extenstion): string
    {
        $types = config('media.types');

        if (!isset($types)) {
            return config('m')
        }

        return 'file';
    }

}
