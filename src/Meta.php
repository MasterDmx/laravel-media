<?php

namespace MasterDmx\LaravelMedia;

interface Meta
{
    public function toArray(): array;

    public static function fromArray(array $data);
}
