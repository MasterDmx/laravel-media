<?php

namespace MasterDmx\LaravelMedia\Contexts;

interface Context
{
    public static function name(): string;

    public function rules(): array;

    public function variations(): array;
}
