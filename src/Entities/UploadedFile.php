<?php

namespace MasterDmx\LaravelMedia\Entities;

use Illuminate\Http\File;

class UploadedFile extends File
{
    public function __construct(string $path, bool $checkPath = true)
    {
        if ($checkPath && !is_file($path)) {
            throw new FileNotFoundException($path);
        }

        parent::__construct($path);
    }
}
