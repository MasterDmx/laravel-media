<?php

namespace MasterDmx\LaravelMedia\Contexts;

class DefaultContext implements Context
{
    public static function name(): string
    {
        return 'default';
    }

    public function rules(): array
    {
        return [];
    }

    public function variations(): array
    {
        return [];
    }
}
