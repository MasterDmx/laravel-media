<?php

namespace MasterDmx\LaravelMedia\Entities\Media;

use MasterDmx\LaravelMedia\Entities\Media;

class Image extends Media
{
    public $title;

    public static function instance(array $data)
    {
        return new static ($data['path'], $data['type'], $data['key'] ?? null, $data['title'] ?? null);
    }

    public function __construct(string $path, string $type = null, string $key = null, string $title = null)
    {
        parent::__construct($path, $type, $key);
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

    /**
     * Показ
     *
     * @param string $template
     * @return string
     */
    public function show(string $template = null): string
    {
        $template = parent::show($template ?? config('default_image_show_template', '<img src="{url}" alt="{title}">'));
        $template = str_replace('{title}', $this->title, $template);

        return $template;
    }
}
