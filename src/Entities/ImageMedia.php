<?php

namespace MasterDmx\LaravelMedia\Entities;

class ImageMedia extends Media
{
    public $title;

    public static function import(array $data)
    {
        return new static ($data['id'], $data['path'], $data['type'], $data['title'] ?? null);
    }

    public function __construct(string $id, string $path, string $type = null, string $title = null)
    {
        parent::__construct($id, $path, $type);
        $this->title = $title;
    }

    public function toArray(): array
    {
        return parent::toArray() + [
            'title' => $this->title
        ];
    }

    public function export(): array
    {
        return parent::export() + [
            'title' => $this->title
        ];
    }
}
